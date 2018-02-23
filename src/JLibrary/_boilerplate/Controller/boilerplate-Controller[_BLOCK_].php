<?php 

namespace J29Bundle\Controller\^^ENTITY_TYPE^^;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use J29Bundle\Entity\^^ENTITY_TYPE^^\**ENTITY**;
/*use J29Bundle\Entity\^^ENTITY_TYPE^^\collection\***CollectionSingleEntity***;*/
use J29Bundle\Form\^^ENTITY_TYPE^^\**ENTITY**Type;
use JLibrary\Traits\ControllerTraits;

/**
 * **ENTITY**
 * 
 * @Route("/admin/^^ENTITY_TYPE^^s")
 */
class **ENTITY**Controller extends Controller
{
    use ControllerTraits;

    const ENTITY_NAMESPACE = 'J29Bundle:^^ENTITY_TYPE^^\**ENTITY**';
    const UNIQUE_NAME = 'block_**SINGLE_ENTITY**';
    const TEMPLATE = 'J29Bundle:^^ENTITY_TYPE^^:^^ENTITY_TYPE^^-__SINGLE-ENTITY__.html.twig';

    private $template_vars = array(
        'form_size' => '###',
        'page_description' => 'Admin Page',
    );
    
    /**
     * @Route("/__SINGLE-ENTITY__", name="j29.^^ENTITY_TYPE^^.**SINGLE_ENTITY**.manage")
     */
    public function manageAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = $this->getEntityData();

        $form = $this->createForm(**ENTITY**Type::class, $data['entity']);
        $form->handleRequest($request);

        if ($form->isValid()){
            // persist and sanitize
            if($data['persist']) $em->persist($data['entity']);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add('success', 'Successfully saved');
            return $this->redirectToRoute('j29.admin.index');
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        // template data
        $build = array_merge([
            'page_title' => '###',
            'form' => $form->createView(),
            'creating_entity' => $data['creating_entity'],
        ], $this->template_vars);

        return $this->render(self::TEMPLATE, $build);
    }

    private function getEntityData(){
        $specs = [
            'persist' => false,
            'entity' => null,
            'creating_entity' => false,
        ];

        // find the entity
        $entity = $this->getDoctrine()
            ->getRepository(self::ENTITY_NAMESPACE)
            ->find(self::UNIQUE_NAME);

        // create initial entity if it doesn't exist
        if(!$entity){
            $entity = new **ENTITY**();
            $entity->setId(self::UNIQUE_NAME);

            /*$collection_single = new ***CollectionSingleEntity***();
            $entity->add***($collection_single);*/

            $specs['persist'] = true;
            $specs['creating_entity'] = true;
        }

        $specs['entity'] = $entity;

        return $specs;
    }
}