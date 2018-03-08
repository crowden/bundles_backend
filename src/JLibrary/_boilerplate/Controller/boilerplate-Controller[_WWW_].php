<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JLibrary\Traits\PublicTemplateRendering;

use J29Bundle\Entity\**ENTITY_TYPE**\**ENTITY**;

class InteriorBasicController extends Controller
{
    use PublicTemplateRendering;
    
    /**
     * @Route("/{urlSlug}", name="j29.**ENTITY_TYPE**.###")
     */
    public function indexAction(**ENTITY** $entity, Environment $twig){
        // template variables
        $build = [
            'page_description' => $entity->getPageDescription(),
            'page_title' => $entity->getPageTitle(),
            'entity' => $entity,
        ];
        
        // custom templates must match slug in naming
        $template_custom = 'J29Bundle:www/**CUSTOM_FOLDER**:' . $entity->getUrlSlug() . '.html.twig';
        $template_general = 'J29Bundle:www:**template-name**.html.twig';

        $template_to_use = $twig->getLoader()->exists($template_custom) ? 
            $template_custom : 
            $template_general;

        return $this->renderByPublishedStatus($entity, $template_to_use, $build);
    }
}