<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Labor;
use App\Models\OperatingExpense;
use App\Models\ProjectStatusHistory;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::connection();

        $activeProjects = (int) $db->query(
            "SELECT COUNT(*) FROM projects WHERE status IN ('cotizado', 'en_proceso')"
        )->fetchColumn();

        $totalReceivable = (float) $db->query("
            SELECT COALESCE(SUM(
                p.agreed_cost + COALESCE(extras.total, 0) - COALESCE(pay.total, 0)
            ), 0)
            FROM projects p
            LEFT JOIN (SELECT project_id, SUM(amount) AS total FROM project_extras GROUP BY project_id) extras
                ON extras.project_id = p.id
            LEFT JOIN (SELECT project_id, SUM(amount) AS total FROM payments GROUP BY project_id) pay
                ON pay.project_id = p.id
            WHERE p.status NOT IN ('cancelado')
        ")->fetchColumn();

        $expenseModel = new Expense();
        $payableToSuppliers = $expenseModel->totalPendingToSuppliers();

        $paymentModel = new Payment();
        $laborModel = new Labor();
        $operatingModel = new OperatingExpense();

        $yearMonth = date('Y-m');
        $monthlyIncome = $paymentModel->monthlyTotal($yearMonth);
        $monthlyDirectExpenses = $expenseModel->monthlyTotal($yearMonth);
        $monthlyLabor = $laborModel->monthlyTotal($yearMonth);
        $monthlyOperating = $operatingModel->monthlyTotal($yearMonth);

        $monthlyExpenses = $monthlyDirectExpenses + $monthlyLabor + $monthlyOperating;
        $monthlyNetUtility = $monthlyIncome - $monthlyExpenses;

        $recentProjects = $db->query("
            SELECT p.id, p.name, p.status, c.name AS client_name
            FROM projects p INNER JOIN clients c ON c.id = p.client_id
            ORDER BY p.created_at DESC LIMIT 5
        ")->fetchAll();

        // Serie de los últimos 6 meses (incluyendo el actual) para la gráfica.
        $monthLabels = [];
        $monthIncome = [];
        $monthExpenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("-{$i} months"));
            $monthLabels[] = date('M Y', strtotime("-{$i} months"));
            $income = $paymentModel->monthlyTotal($ym);
            $expenses = $expenseModel->monthlyTotal($ym) + $laborModel->monthlyTotal($ym) + $operatingModel->monthlyTotal($ym);
            $monthIncome[] = round($income, 2);
            $monthExpenses[] = round($expenses, 2);
        }

        $alertModel = new ProjectStatusHistory();

        $this->view('dashboard/index', [
            'activeProjects'     => $activeProjects,
            'totalReceivable'    => $totalReceivable,
            'payableToSuppliers' => $payableToSuppliers,
            'monthlyIncome'      => $monthlyIncome,
            'monthlyExpenses'    => $monthlyExpenses,
            'monthlyNetUtility'  => $monthlyNetUtility,
            'recentProjects'     => $recentProjects,
            'monthLabels'        => $monthLabels,
            'monthIncome'        => $monthIncome,
            'monthExpenses'      => $monthExpenses,
            'upcomingDeliveries' => $alertModel->upcomingDeliveries(7),
            'overdueBalances'    => $alertModel->overdueBalances(),
        ]);
    }
}
