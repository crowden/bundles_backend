<?php 

namespace JLibrary\Traits;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait PublicTemplateRendering
{
    protected function renderByPublishedStatus($entity, $template, $build){
        // entity is published for all to see
        if ($entity->getPublished()){
            $build['notify_unpublished'] = false;
            return $this->render($template, $build);
        }

        // entity is unpublished, but they are an admin
        if (!$entity->getPublished() && $this->isGranted('ROLE_ADMIN')){
            $build['notify_unpublished'] = true;
            return $this->render($template, $build);
        }

        // trying to view as anonymous. Issue 403
        if (!$entity->getPublished() && !$this->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedHttpException('Not allowed to view this page');
        }
    }
}