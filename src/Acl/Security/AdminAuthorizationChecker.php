<?php

namespace Lle\EasyAdminPlusBundle\Acl\Security;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Pierre-Charles Bertineau <pc.bertineau@alterphp.com>
 */
class AdminAuthorizationChecker
{
    private $authorizationChecker;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Throws an error if user has no access to the entity action.
     *
     * @param array  $entity
     * @param string $actionName
     */
    public function checksUserAccess(array $entity, string $actionName, $subject)
    {
        $requiredRole = $this->getRequiredRole($entity, $actionName);
        if ($requiredRole && !$this->authorizationChecker->isGranted($requiredRole, $subject)) {
            throw new AccessDeniedException(
                sprintf('You must be granted %s role to perform this entity action !', $requiredRole)
            );
        }
    }

    public function hasRole($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    public function isEasyAdminGranted(array $entity, string $actionName, $subject)
    {
        try {
            $this->checksUserAccess($entity, $actionName, $subject);
        } catch (AccessDeniedException $e) {
            return false;
        }

        return true;
    }

    protected function getRequiredRole(array $entity, string $actionName)
    {
        if (isset($entity[$actionName]) && isset($entity[$actionName]['role'])) {
            return $entity[$actionName]['role'];
        } elseif (isset($entity['role_prefix'])) {
            return $entity['role_prefix'].'_'.strtoupper($actionName);
        }

        return $entity['role'] ?? null;
    }
}
