<?php

declare(strict_types=1);

namespace Odiseo\SyliusRbacPlugin\Action;

use Odiseo\SyliusRbacPlugin\Message\CreateAdministrationRole;
use Odiseo\SyliusRbacPlugin\Normalizer\AdministrationRolePermissionNormalizerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateAdministrationRoleAction
{
    /** @var MessageBusInterface */
    private $bus;

    /** @var AdministrationRolePermissionNormalizerInterface */
    private $administrationRolePermissionNormalizer;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        MessageBusInterface $bus,
        AdministrationRolePermissionNormalizerInterface $administrationRolePermissionNormalizer,
        Session $session,
        UrlGeneratorInterface $router
    ) {
        $this->bus = $bus;
        $this->administrationRolePermissionNormalizer = $administrationRolePermissionNormalizer;
        $this->session = $session;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
    {
        try {
            /** @var array $administrationRolePermissions */
            $administrationRolePermissions = $request->request->get('permissions');

            $normalizedPermissions = $this
                ->administrationRolePermissionNormalizer
                ->normalize($administrationRolePermissions)
            ;

            /** @var string $administrationRoleName */
            $administrationRoleName = $request->request->get('administration_role_name');

            $this->bus->dispatch(new CreateAdministrationRole(
                $administrationRoleName,
                $normalizedPermissions
            ));

            $this->session->getFlashBag()->add(
                'success',
                'sylius_rbac.administration_role_successfully_created'
            );
        } catch (\Exception $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse($this->router->generate('sylius_rbac_admin_administration_role_create_view', []));
    }
}
