<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\**ENTITY_TYPE**\**ENTITY**;

class InteriorBasicController extends Controller
{
    /**
     * @Route("/{urlSlug}", name="j29.**ENTITY_TYPE**.###")
     */
    public function indexAction(**ENTITY** $entity){
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
        $template_custom = 'J29Bundle:**ENTITY_TYPE**/**BASE_TEMPLATE**:' . $entity->getUrlSlug() . '.html.twig';
        $template_general = 'J29Bundle:**ENTITY_TYPE**:**BASE-TEMPLATE**.html.twig';
        $template_unauthorized = 'JLibrary:templates/unpublished:unauthorized.html.twig';
        
        // dynamically set template
        $template_to_use = $this
            ->get('twig')
            ->getLoader()
            ->exists($template_custom) ? $template_custom : $template_general;

        // entity is published for all to see
        if ($published) return $this->render($template_to_use, $build);

        // entity is unpublished, but they are an admin
        if (!$published && $admin){
            $build['notify_unpublished'] = true;
            return $this->render($template_to_use, $build);
        }

        // trying to view as anonymous
        if (!$published && !$admin) return $this->render($template_unauthorized);
    }
}