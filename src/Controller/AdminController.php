<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRoles;
use App\Service\Admin\AdminService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRoles::ADMIN->value)]
#[Route('/admin')]
final class AdminController extends BaseController
{
    public function __construct(private AdminService $adminService)
    {
    }

    #[Route('/', name: 'admin', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $this->adminService->getAllUsers(),
            'role_admin' => UserRoles::ADMIN->value,
        ]);
    }

    #[Route('/table', name: 'admin_table', methods: ['GET'])]
    public function table(): Response
    {
        return $this->render('admin/user_table.html.twig', [
            'users' => $this->adminService->getAllUsers(),
            'role_admin' => UserRoles::ADMIN->value,
        ]);
    }

    #[Route('/block', name: 'admin_block', methods: ['POST'])]
    public function blockUser(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json(
                $this->jsonErrorData
            );
        }

        $this->addRedirectIfUserInArray($userIds);
        $this->adminService->blockUsers($userIds);

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/unblock', name: 'admin_unblock', methods: ['POST'])]
    public function unblockUser(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json($this->jsonErrorData);
        }

        $this->adminService->unblockUsers($userIds);

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/delete', name: 'admin_delete', methods: ['POST'])]
    public function deleteUser(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json($this->jsonErrorData);
        }

        $this->addRedirectIfUserInArray($userIds);
        $this->adminService->deleteUsers($userIds);

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/make/admin', name: 'admin_make_admin', methods: ['POST'])]
    public function makeAdmin(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json($this->jsonErrorData);
        }

        $this->adminService->makeAdminUsers($userIds);

        return $this->json($this->jsonSuccessData);
    }

    #[Route('/unmake/admin', name: 'admin_unmake_admin', methods: ['POST'])]
    public function unmakeAdmin(Request $request): Response
    {
        $userIds = $request->request->all('userIds');

        if (empty($userIds)) {
            return $this->json($this->jsonErrorData);
        }

        $this->addRedirectIfUserInArray($userIds);
        $this->adminService->unmakeAdminUsers($userIds);

        return $this->json($this->jsonSuccessData);
    }

    private function addRedirectIfUserInArray(array $userIds)
    {
        /** @var ?User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser && in_array($currentUser->getId(), $userIds)) {
            $this->addRedirect($this->generateUrl('home'));
        }
    }
}
