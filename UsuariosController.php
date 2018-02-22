<?php

namespace RDER\SFPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use RDER\SFPBundle\Entity\Usuarios;
use RDER\SFPBundle\Entity\Cecos;
use RDER\SFPBundle\Entity\Usuariocecos;
use RDER\SFPBundle\Form\UsuariosType;


//use RDER\SFPBundle\Entity\Cecos;

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
       

        // CONSULTAS
    	$directorio = $em->getRepository('RDERSFPBundle:Directorio')->findAll();
    	$cecos = $em->getRepository('RDERSFPBundle:Cecos')->findBy(array(), array('ceco' => 'ASC'));
        $ultimoId= $em->getRepository('RDERSFPBundle:Usuarios')->buscarUltimoId();
        $form   = $this->createCreateForm($nuevoUsuario);
        $form->handleRequest($request);
      
         if ($form->isSubmitted() && $form->isValid())
            {
           // var_dump($_POST);exit(1);
              $nuevoUsuario->setNombre($_POST["usuarios"]["nombre"]);
              $nuevoUsuario->setUsuario($_POST["usuarios"]["usuario"]); 
              $nuevoUsuario->setArea($_POST["usuarios"]["area"]);
             // $nuevoUsuario->setRole($_POST["usuarios"]["role"]);
              $nuevoUsuario->setEstado($_POST["usuarios"]["estado"]);
               $em->persist($nuevoUsuario);
                $em->flush(); 
             $aux=(count($nuevoUsuario->getUsuariocecosrel()));
            // var_dump($nuevoUsuario);exit(1);   
             for ($i=0; $i <=$aux ; $i++) 
                { 
                    if ($nuevoUsuario->getUsuariocecosrel()[$i]!=null) {
                        $relacion = new Usuariocecos();
                        $ultimoId= $em->getRepository('RDERSFPBundle:Usuarios')->buscarUltimoId();
                       $idceco=(int)$_POST["usuarios"]["Usuariocecosrel"][$i]["ceco"];
                       $role=(int)$_POST["usuarios"]["Usuariocecosrel"][$i]["role"];
                       $ceco=$em->getRepository('RDERSFPBundle:Cecos')->find($idceco);
                       $user=$em->getRepository('RDERSFPBundle:Usuarios')->find($ultimoId[0][1]);
                       $relacion->setCeco($ceco);
                       $relacion->setUsuario($user);
                        $relacion->setRole($role);
                        $em->persist($relacion);
                        $em->flush();   
           
                       
                    }
                }
             
                $usuarios = $em->getRepository('RDERSFPBundle:Usuarios')->findBy(array(), array('id' => 'DESC'));
                $mensaje = array('mensaje' => 'Se ha creado el Usuario correctamente');  
                return $this->render('RDERSFPBundle:Usuarios:index.html.twig', array(
                                     'users' => $usuarios,
                                     'mensaje' => $mensaje));
    
         }

    	return $this->render('RDERSFPBundle:Usuarios:nuevo.html.twig', array(
    		'listaceco' => $cecos,
               'ultimoId' => $ultimoId[0][1],
            'form'   => $form->createView()));
    }


    // USUARIO - EDITAR USUARIO
    public function editarAction(Request $request, $id)
    {
       
        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('RDERSFPBundle:Usuarios')->find($id);


        if (!$usuario) {
            throw $this->createNotFoundException('No existe el Usuario con ese ID');
        }
        
        $usuariocecos=$em->getRepository('RDERSFPBundle:Usuarios')->buscarUsuarioCecos($id);    
        
        $cecos = $em->getRepository('RDERSFPBundle:Cecos')->findAll();
      
           foreach ($usuariocecos as $usuarioceco) {
                $usuario->addUsuariocecosrel($usuarioceco);
            }
    
        $editForm = $this->createEditForm($usuario);
        $editForm->handleRequest($request);
        //var_dump($usuario->getUsuariocecosrel());exit(1);
        // inicio
   
            

        //-- Con esto nuestro formulario ya es capaz de decirnos si
        //   los dato son válidos o no y en caso de ser así
      
            
        
         if ($editForm->isSubmitted() && $editForm->isValid())
            {
    
                //$usuario->setRole($_POST["usuarios"]["role"]);
                $usuario->setEstado($_POST["usuarios"]["estado"]);
               //  var_dump($usua);exit(1);
                $aux=(count($usuario->getUsuariocecosrel()));
                
                for ($i=0; $i <=$aux ; $i++) 
                {   
                    if ($usuario->getUsuariocecosrel()[$i]!=null) {
                        $relacion=array();
                    // var_dump($_POST);exit(1);
                      
                        $idusuario=(int)$_POST["usuarios"]["Usuariocecosrel"][$i]["usuario"];
                        $idceco=(int)$_POST["usuarios"]["Usuariocecosrel"][$i]["ceco"];
                        
                        $relacion=$em->getRepository('RDERSFPBundle:Usuarios')->buscar($idusuario,$idceco);
                        
                        if ((count($relacion)==0)) 
                        {
                            
                           $relacion = new Usuariocecos();
                           $relacion=$usuario->getUsuariocecosrel()[$i];
                       
                            $em->persist($relacion);
                            $em->flush();
                         
                        }
                        
                    }
                    if ($usuario->getUsuariocecosrel()[$i]==null || $relacion!=null) {
                    
                        $relaciones=$em->getRepository('RDERSFPBundle:Usuarios')->buscarrelacion($usuario->getId()+0);
                        foreach ($relaciones  as $relacion) {
                            if (!$usuario->getUsuariocecosrel()->contains($relacion)) {
                               
                                $em->remove($relacion);
                                $em->flush(); 
                            } 
                        }
                    }
                    $em->persist($usuario);
                    $em->flush();  
                }
              
                $usuarios = $em->getRepository('RDERSFPBundle:Usuarios')->findBy(array(), array('id' => 'DESC'));
                $mensaje = array('mensaje' => 'Se ha guardado la información correctamente');  
                return $this->render('RDERSFPBundle:Usuarios:index.html.twig', array(
                                     'users' => $usuarios,
                                     'mensaje' => $mensaje));

        // fin
        
        }
        return $this->render('RDERSFPBundle:Usuarios:editar.html.twig', array(
            'listaceco' => $cecos,
            'usuario' => $usuario,
             'form'   => $editForm->createView()));

    }
    public function buscarcecoxuserAction($idusuario)  //ACT
    {
       $em = $this->getDoctrine()->getManager();
        $v = $em->getRepository('RDERSFPBundle:Usuarios')->buscarCecosUser($idusuario);
        $v["success"] = true;
        $v["data"]["message"] = "fine";
        $v["data"]["usuario"] = $idusuario;
        $v["data"]["tamano"] = count($v);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($v, JSON_FORCE_OBJECT);
        exit();
    } 
    
     /**
    * Creates a form to edit a Variable entity.
    *
    * @param Variable $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuarios $entity)
    {
      // var_dump($entity);exit(1);
       /* $form = $this->createFormBuilder($entity, array(
            'action' => $this->generateUrl('rdersfp_admin_usuarios_editar', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));*/
        // var_dump($entity);exit(1);
        $form = $this->createForm(UsuariosType::class, $entity, array(
            'action' => $this->generateUrl('rdersfp_admin_usuarios_editar', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit',SubmitType::class, array('label' => 'Actualizar', 'attr' => array('class' => 'btn btn-primary')));
       //var_dump($form);exit(1);

        return $form;
    }
    
      /**
     * Creates a form to create a Variable entity.
     *
     * @param Variable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Usuarios $entity)  //ACT
    {
        $form = $this->createForm(UsuariosType::class, $entity, array(
            'action' => $this->generateUrl('rdersfp_admin_usuarios_nuevo'),
            'method' => 'POST',
        ));
        $form->add('nombre', TextType::class);
        $form->add('usuario', TextType::class);
        $form->add('area', TextType::class);
        $form->add('submit', SubmitType::class, array('label' => 'Crear', 'attr' => array('class' => 'btn btn-primary',)));
        

        return $form;
    }


     public function BuscarUsuariosAction()
    {
       $encoders = array(new XmlEncoder(), new JsonEncoder());
       $normalizers = array(new GetSetMethodNormalizer());
       $serializer = new Serializer($normalizers, $encoders);
        
        $em = $this->getDoctrine()->getManager();
         
        $usuarios = $em->getRepository('RDERSFPBundle:Usuarios')->findBy(array(), array('nombre' => 'ASC'));
       // var_dump(count($usuarios));exit(1);
        $listausuarios = array(); 
        
        for ($i=0; $i < count($usuarios); $i++){
        
         $listausuarios[] = array(
                      
                    "id" =>  $usuarios[$i]->getId(),
                    "nombre" =>  $usuarios[$i]->getNombre(),
                    "usuario" =>  $usuarios[$i]->getUsuario(),
                    "area" =>  $usuarios[$i]->getArea(),
                    "role" =>  $usuarios[$i]->getRole(),
                    "estado" =>  $usuarios[$i]->getEstado());
                      
        }  
        
     $jsonContent = $serializer->serialize(array("data"=>$listausuarios), 'json');
 
      return new response($jsonContent);

    }
    
}
