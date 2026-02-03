<?php

require_once __DIR__ . '/../models/Building.php';
require_once __DIR__ . '/../models/Tenant.php';
require_once __DIR__ . '/../models/Rent.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Request.php';
require_once __DIR__ . '/../core/Auth.php';

class AdminController
{
    public function dashboard(): array
    {
        $buildingModel = new Building();
        $tenantModel   = new Tenant();
        $paymentModel  = new Payment();
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();

        $buildings = $buildingModel->all();
        $tenants   = $tenantModel->allWithUnits();

        $overdueRents = $paymentModel->overdueRents();
        $recentPayments = $paymentModel->recent(10);
        $pendingProofs = $paymentModel->getPendingProofs();
        $totalCollected = $paymentModel->totalCollected();

        // Get all users for admin management
        $users = $userModel->allWithStatus();
        Auth::startSession();
        $currentUser = Auth::user();
        $requestModel = new Request();
        if ($currentUser && $currentUser['role_name'] === 'manager') {
            $requests = $requestModel->forManager((int)$currentUser['id'], 50);
        } else {
            $requests = $requestModel->recentWithDetails(50);
        }

        return [
            'stats' => [
                'total_buildings' => count($buildings),
                'total_tenants'   => count($tenants),
                'paid_total'      => (float)$totalCollected,
                'overdue_count'   => count($overdueRents),
            ],
            'overdueRents'   => $overdueRents,
            'recentPayments' => $recentPayments,
            'users' => $users,
            'buildings' => $buildings,
            'requests' => $requests,
            'pendingProofs' => $pendingProofs,
        ];
    }
}
