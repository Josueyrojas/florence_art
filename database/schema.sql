-- =====================================================================
-- M. Florence Art — Sistema de Control Financiero y Gestión de Proyectos
-- Motor recomendado: MySQL 8.0+ / MariaDB 10.4+
-- =====================================================================

-- En hosting compartido/gratuito (InfinityFree y similares) la cuenta ya
-- trae una base de datos creada con su propio nombre (algo como
-- if0_XXXXXXX_nombre) y normalmente NO tiene permiso para crear otra.
-- En ese caso, antes de importar por phpMyAdmin, borra o comenta las
-- siguientes 2 sentencias (CREATE DATABASE / USE) — phpMyAdmin ya
-- ejecuta el resto del script sobre la base que tengas seleccionada.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS florence_art
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE florence_art;

-- ---------------------------------------------------------------------
-- Usuarios del sistema
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120)  NOT NULL,
    email           VARCHAR(150)  NOT NULL UNIQUE,
    password_hash   VARCHAR(255)  NOT NULL,
    role            ENUM('admin','operador') NOT NULL DEFAULT 'operador',
    is_active       TINYINT(1)    NOT NULL DEFAULT 1,
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    TIMESTAMP NULL DEFAULT NULL,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Configuración del negocio (fila única: id = 1)
-- ---------------------------------------------------------------------
CREATE TABLE settings (
    id            TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    business_name VARCHAR(150) NOT NULL DEFAULT 'Florence Art',
    tagline       VARCHAR(255) DEFAULT NULL,
    phone         VARCHAR(30)  DEFAULT NULL,
    whatsapp      VARCHAR(30)  DEFAULT NULL,
    email         VARCHAR(150) DEFAULT NULL,
    address       VARCHAR(255) DEFAULT NULL,
    website       VARCHAR(150) DEFAULT NULL,
    logo_path     VARCHAR(255) DEFAULT NULL,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (id, business_name, tagline, phone, whatsapp, website) VALUES
    (1, 'Florence Art', 'Diseño, fabricación y restauración de muebles finos', '+52 55 397231', '+52 55 4131 2421', 'www.florence-art.com.mx');

-- ---------------------------------------------------------------------
-- Clientes
-- ---------------------------------------------------------------------
CREATE TABLE clients (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    phone      VARCHAR(30),
    email      VARCHAR(150),
    address    VARCHAR(255),
    notes      TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clients_name (name)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Proveedores
-- ---------------------------------------------------------------------
CREATE TABLE suppliers (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    contact_name  VARCHAR(150),
    phone         VARCHAR(30),
    email         VARCHAR(150),
    address       VARCHAR(255),
    notes         TEXT,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_suppliers_name (name)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Proyectos
-- ---------------------------------------------------------------------
CREATE TABLE projects (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id      INT UNSIGNED NOT NULL,
    name           VARCHAR(180) NOT NULL,
    description    TEXT,
    agreed_cost    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    start_date     DATE,
    delivery_date  DATE,
    status         ENUM('cotizado','en_proceso','entregado','finalizado','cancelado')
                   NOT NULL DEFAULT 'cotizado',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_projects_client FOREIGN KEY (client_id) REFERENCES clients(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_projects_status (status),
    INDEX idx_projects_client (client_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Bitácora de cambios de estado del proyecto
-- ---------------------------------------------------------------------
CREATE TABLE project_status_history (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    old_status  ENUM('cotizado','en_proceso','entregado','finalizado','cancelado') NULL,
    new_status  ENUM('cotizado','en_proceso','entregado','finalizado','cancelado') NOT NULL,
    changed_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_status_history_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_status_history_project (project_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Extras / modificaciones al costo acordado (cambios a mitad de proyecto)
-- ---------------------------------------------------------------------
CREATE TABLE project_extras (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount      DECIMAL(12,2) NOT NULL, -- positivo = cargo adicional, negativo = descuento
    extra_date  DATE NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_extras_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_extras_project (project_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Galería / evidencias del proyecto (bocetos, fotos, documentos)
-- ---------------------------------------------------------------------
CREATE TABLE project_media (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    file_path   VARCHAR(255) NOT NULL,
    media_type  ENUM('boceto','foto','documento','otro') NOT NULL DEFAULT 'foto',
    caption     VARCHAR(255),
    uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_media_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_media_project (project_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Abonos / pagos de clientes (ingresos)
-- ---------------------------------------------------------------------
CREATE TABLE payments (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id     INT UNSIGNED NOT NULL,
    amount         DECIMAL(12,2) NOT NULL,
    payment_date   DATE NOT NULL,
    payment_method ENUM('efectivo','transferencia','tarjeta','deposito','otro')
                   NOT NULL DEFAULT 'efectivo',
    notes          VARCHAR(255),
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_payments_project (project_id),
    INDEX idx_payments_date (payment_date)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Gastos directos del proyecto (materiales, herrajes, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE expenses (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id     INT UNSIGNED NOT NULL,
    supplier_id    INT UNSIGNED NULL,
    category       ENUM('material','herraje','acabado','transporte','otro')
                   NOT NULL DEFAULT 'material',
    description    VARCHAR(255) NOT NULL,
    amount         DECIMAL(12,2) NOT NULL,
    expense_date   DATE NOT NULL,
    is_credit      TINYINT(1) NOT NULL DEFAULT 0, -- 1 = comprado "al fiado"
    payment_status ENUM('pagado','pendiente') NOT NULL DEFAULT 'pagado',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_expenses_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_expenses_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_expenses_project (project_id),
    INDEX idx_expenses_supplier (supplier_id),
    INDEX idx_expenses_credit (is_credit, payment_status)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Abonos a proveedores (pagos de deuda / fiado)
-- ---------------------------------------------------------------------
CREATE TABLE supplier_payments (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id    INT UNSIGNED NOT NULL,
    amount         DECIMAL(12,2) NOT NULL,
    payment_date   DATE NOT NULL,
    payment_method ENUM('efectivo','transferencia','tarjeta','deposito','otro')
                   NOT NULL DEFAULT 'efectivo',
    notes          VARCHAR(255),
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_supplier_payments_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_supplier_payments_supplier (supplier_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Mano de obra (interna o externa) por proyecto
-- ---------------------------------------------------------------------
CREATE TABLE labor_costs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    worker_name VARCHAR(150),
    labor_type  ENUM('interno','externo') NOT NULL DEFAULT 'interno',
    description VARCHAR(255),
    hours       DECIMAL(6,2),
    amount      DECIMAL(12,2) NOT NULL,
    labor_date  DATE NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_labor_project FOREIGN KEY (project_id) REFERENCES projects(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_labor_project (project_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Gastos operativos / generales del negocio (no ligados a un proyecto)
-- ---------------------------------------------------------------------
CREATE TABLE operating_expenses (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category      ENUM('renta','servicios','insumos_generales','mantenimiento','nomina_admin','otro')
                  NOT NULL DEFAULT 'otro',
    description   VARCHAR(255) NOT NULL,
    amount        DECIMAL(12,2) NOT NULL,
    expense_date  DATE NOT NULL,
    is_recurring  TINYINT(1) NOT NULL DEFAULT 0,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_operating_date (expense_date)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- Datos de ejemplo (opcional, útil para probar el sistema de inmediato)
-- =====================================================================
-- INSERT INTO clients (name, phone, email) VALUES
--   ('Familia Gómez', '5512345678', 'gomez@example.com');
--
-- INSERT INTO projects (client_id, name, description, agreed_cost, start_date, status) VALUES
--   (1, 'Comedor de roble 6 plazas', 'Mesa y sillas en madera de roble', 25000.00, '2026-08-01', 'en_proceso');
