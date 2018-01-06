<?php 

namespace J29Bundle\Controller\^^ENTITY_TYPE^^;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\^^ENTITY_TYPE^^\**ENTITY**;
use J29Bundle\Form\^^ENTITY_TYPE^^\**ENTITY**Type;
use JLibrary\Traits\ControllerTraits;

/**
 * **ENTITY**
 * 
 * @Route("/admin")
 */
class _***_Controller extends Controller
{
    use ControllerTraits;

    const ENTITY_NAMESPACE = 'J29Bundle:^^ENTITY_TYPE^^\**ENTITY**';
    const UNIQUE_NAME = 'block_**SINGLE_ENTITY**';
    const TEMPLATE = 'J29Bundle:^^ENTITY_TYPE^^:^^ENTITY_TYPE^^-__SINGLE-ENTITY__.html.twig';

    /**
     * types include:
     *     - plain_text
     *     - url
     *     - url_validated
     *     - email_address
     *     - markdown_general
     */
    private $sanitize_options = array(
        'EntityPrivateProperty' => [
            'type' => 'plain_text',
            'optional' => false,
        ]
    );

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
    );
    
    /**
     * @Route("/^^ENTITY_TYPE^^s/__SINGLE-ENTITY__", name="j29.^^ENTITY_TYPE^^.**SINGLE_ENTITY**.manage")
     */
    public function manageAction(Request $request){
        $entity = $this
            ->getDoctrine()
            ->getRepository(self::ENTITY_NAMESPACE)
            ->find(self::UNIQUE_NAME);

        if(!$entity){
            $entity = new **ENTITY**();
            $entity->setId(self::UNIQUE_NAME);
            $current_action = 'create';
        } else {
            $current_action = 'edit';
        }

        $form = $this->createForm(**ENTITY**Type::class, $entity);
        $form->handleRequest($request);

        if ($form->isValid()){
            // sanitize, persist, and redirect
            $this->sanitizeAndPersist($entity, $current_action);
            $request->getSession()->getFlashBag()->add('success', 'Successfully saved');
            
            return $this->redirectToRoute('j29.admin.index');
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'creating_entity' => true,
            'page_title' => 'New ###',
            'form' => $form->createView(),
        ], $this->template_vars);

        return $this->render(self::TEMPLATE, $build);
    }
}