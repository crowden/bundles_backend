<?php 

namespace J29Bundle\Controller\**ENTITY_TYPE**;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use J29Bundle\Entity\**ENTITY_TYPE**\**ENTITY**;
use J29Bundle\Form\**ENTITY_TYPE**\**ENTITY**Type;

use JLibrary\Traits\ControllerTraits;

class **ENTITY**Controller extends Controller
{
    use ControllerTraits;
    
    const ENTITY_NAMESPACE = 'J29Bundle:**ENTITY_TYPE**\**ENTITY**';
    const PAGE_DESC = '###';
    const SUBMIT_MSG = '###';

    /**
     * types include:
     *     - plain_text
     *     - url
     *     - url_validated
     *     - email_address
     *     - markdown_general
     */
    private $sanitize_options = array(
        'Name' => [
            'type' => 'plain_text',
            'optional' => false,
        ],
    );

    /**
     * @Route("/###", name="j29.**ENTITY_TYPE**.**entity_lowercase**.**action_type**")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function **action_type**Action(Request $request){
        $entity = new **ENTITY**();

        // form creation
        $form = $this->createForm(**ENTITY**Type::class, $entity);
        $form->handleRequest($request);

        // form submission
        if ($form->isValid()){
            // STEP #1: Check if honeypot has been filled out
            $honeypot = $form['j29FormCode']->getData();
            $honeypot_set = (strlen($honeypot) > 0);
            
            // simply redirect without explanation
            if ($honeypot_set) return $this->redirectToRoute('j29.www.index');

            $this->sanitizeAndPersist($entity, 'create'); // sanitize and persist
            $this->addFlash('success', self::SUBMIT_MSG); // successful confirmation msg
            return $this->redirectToRoute('j29.**ENTITY_TYPE**.index');
        } else {
            if ($form->isSubmitted()) $this->addFlash('warning', 'You have errors with your form.');
        }

        $build = [
            'page_title' => '###',
            'form_size' => 'small',
            'page_description' => self::PAGE_DESC,
            'form' => $form->createView(),
        ];

        return $this->render('J29Bundle:**ENTITY_TYPE**:**template**.html.twig', $build);
    }
}