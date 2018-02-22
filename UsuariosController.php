<?php

namespace RDER\SFPBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use RDER\SFPBundle\Entity\Usuarios;
use RDER\SFPBundle\Form\UsuariosType;

use RDER\SFPBundle\Entity\Cecos;
use RDER\SFPBundle\Form\CecosTType;




class UsuariosController extends Controller
{
    // USUARIO - INDEX
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$usuarios = $em->getRepository('RDERSFPBundle:Usuarios')->findBy(array(), array('id' => 'DESC'));

        return $this->render('RDERSFPBundle:Usuarios:index.html.twig', array(
            'users' => $usuarios));
    } 


    // USUARIO - NUEVO USUARIO 
    public function nuevoAction(Request $request)
    {
    	
    	$em = $this->getDoctrine()->getManager();


    	$nuevoUsuario = new Usuarios();
        $nuevoCeco = new Cecos();

        // CONSULTAS
    	$directorio = $em->getRepository('RDERSFPBundle:Directorio')->findAll();
    	$cecos = $em->getRepository('RDERSFPBundle:Cecos')->findBy(array(), array('ceco' => 'ASC'));

        // CREACION DE FORMULARIOS
    	$form = $this->createForm(UsuariosType::class, $nuevoUsuario);
            // CREACION SEGUNDO FORMULARIO PARA SELECCIONAR LOS CENTROS DE COSTOS QUE DESEA
            $form2 = $this->createFormBuilder($nuevoCeco)
                ->add('ceco', EntityType::class, array(
                    'class' => 'RDERSFPBundle:Cecos',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.ceco', 'ASC');
                    },
                    'choice_label' => 'ceco',
                    'multiple' => true))
                ->add('descripcion', EntityType::class, array(
                    'class' => 'RDERSFPBundle:Cecos',
                    'choice_label' => 'descripcion'))
                ->getForm();

        // SI LA RESPUESTA ES UN METODO POST
        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            $form2->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

            }
            if ($form2->isSubmitted() && $form2->isValid()) {

                $data = $request->request->all();
                $user = $em->getRepository('RDERSFPBundle:Directorio')->findBy(array('nombre' => $data["usuarios"]["nombre"]));

                // Recorre el array por si existe mas de un CECO seleccionado
                for ($i=0; $i < count($data["form"]["ceco"]); $i++) { 
                    
                    $CECO = $em->getRepository('RDERSFPBundle:Cecos')->find($data["form"]["ceco"][$i]);

                    $nuevoUsuario->getCecos()->add($CECO);
                    $nuevoUsuario->setArea($user[0]->getArea());
                    $nuevoUsuario->setPassword($user[0]->getPassword());
                    $nuevoUsuario->setEstado(TRUE);

                    $em->persist($nuevoUsuario);

                }
            
                $em->flush();
                return $this->redirectToRoute('rdersfp_admin_usuarios');

            }

        }  

    	return $this->render('RDERSFPBundle:Usuarios:nuevo.html.twig', array(
    		'users' => $directorio, 
    		'cecos' => $cecos,
            'form' => $form->createView(),
            'form2' => $form2->createView()));
    }


    // USUARIO - EDITAR USUARIO
    public function editarAction(Request $request, $id)
    {
       
        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('RDERSFPBundle:Usuarios')->find($id);
        $cecos = $em->getRepository('RDERSFPBundle:Cecos')->findAll();


        if (!$usuario) {
            throw $this->createNotFoundException('No existe el Usuario con ese ID');
        }

        // CREA UN ARRAY CON LOS CECOS RELACIONADOS AL USUARIO A EDITAR
        $array = $usuario->getCecos()->toArray();

        for ($i=0; $i < count($array); $i++) { 
            $array2[] = array(
                "ceco" => $array[$i]->getCeco(),
                "descripcion" => $array[$i]->getDescripcion());
        }

        
        // CREACION DE FORMULARIO CON LAS POSIBLES OPCIONES A EDITAR
        $form = $this->createFormBuilder($usuario)
            ->add('role', ChoiceType::class, array('choices' => array('Administrador' => 1, 'Gestor' => 2, 'Visualizador' => 3)))
            ->add('estado', ChoiceType::class, array('choices' => array('Activo' => TRUE, 'Bloqueado' => FALSE)))
            ->getForm();
        

        $form->handleRequest($request);

        //OBTENER DATOS DE LA RESPUESTA
        $data = $request->request->all();
        var_dump($data);

        return $this->render('RDERSFPBundle:Usuarios:editar.html.twig', array(
            'cecos' => $array2,
            'usuario' => $usuario,
            'form' => $form->createView()));

    }


}
