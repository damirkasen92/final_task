<?php

namespace App\Controller;

use App\Enum\JsonStatuses;
use App\Enum\UserRoles;
use App\Repository\UserRepository;
use App\Service\Admin\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRoles::ADMIN->value)]
#[Route(path: [
    'en' => '/admin',
    'ru' => '/ru/admin',
])]
final class AdminController extends BaseController
{
    public function __construct(private AdminService $adminService)
    {
    }

    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $this->adminService->getAllUsers(),
            'role_admin' => UserRoles::ADMIN->value
        ]);
    }

    #[Route('/table', name: 'admin_table')]
    public function table(): Response
    {
        return $this->render('admin/user_table.html.twig', [
            'users' => $this->adminService->getAllUsers(),
            'role_admin' => UserRoles::ADMIN->value
        ]);
    }

    #[Route('/block', name: 'admin_block')]
    public function blockUser(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json([
                'status' => JsonStatuses::error,
            ]);
        }

        $this->adminService->blockUsers($userIds);

        return $this->json([
            'status' => JsonStatuses::success,
        ]);
    }

    #[Route('/unblock', name: 'admin_unblock', methods: ['POST'])]
    public function unblockUser(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json([
                'status' => JsonStatuses::error,
            ]);
        }

        $this->adminService->unblockUsers($userIds);

        return $this->json([
            'status' => JsonStatuses::success,
        ]);
    }

    #[Route('/delete', name: 'admin_delete', methods: ['POST'])]
    public function deleteUser(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json([
                'status' => JsonStatuses::error,
            ]);
        }

        $this->adminService->deleteUsers($userIds);

        return $this->json([
            'status' => JsonStatuses::success,
        ]);
    }

    #[Route('/make/admin', name: 'admin_make_admin', methods: ['POST'])]
    public function makeAdmin(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json([
                'status' => JsonStatuses::error,
            ]);
        }

        $this->adminService->makeAdminUsers($userIds);

        return $this->json([
            'status' => JsonStatuses::success,
        ]);
    }

    #[Route('/unmake/admin', name: 'admin_unmake_admin', methods: ['POST'])]
    public function unmakeAdmin(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json([
                'status' => JsonStatuses::error,
            ]);
        }

        $this->adminService->unmakeAdminUsers($userIds);

        return $this->json([
            'status' => JsonStatuses::success,
        ]);
    }
}
