<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

use J29Bundle\Entity\**ENTITY_TYPE**\**ENTITY**;

class InteriorBasicController extends Controller
{
    /**
     * @Route("/{urlSlug}", name="j29.**ENTITY_TYPE**.###")
     */
    public function indexAction(**ENTITY** $entity, Environment $twig){
        $published = $entity->getPublished();
        $admin = $this->isGranted('ROLE_ADMIN');
        
        // template variables
        $build = [
            'page_description' => $entity->getPageDescription(),
            'page_title' => $entity->getPageTitle(),
            'entity' => $entity,
            'notify_unpublished' => false,
        ];
        
        // custom templates must match slug in naming
        $template_custom = 'J29Bundle:www/news_article:' . $entity->getUrl() . '.html.twig';
        $template_general = 'J29Bundle:www:news-article.html.twig';

        $template_to_use = $twig->getLoader()->exists($template_custom) ? 
            $template_custom : 
            $template_general; 

        // entity is published for all to see
        if ($published) return $this->render($template_to_use, $build);

        // entity is unpublished, but they are an admin
        if (!$published && $admin){
            $build['notify_unpublished'] = true;
            return $this->render($template_to_use, $build);
        }

        // trying to view as anonymous
        if (!$published && !$admin) throw new AccessDeniedHttpException('Not allowed to view this page');
    }
}