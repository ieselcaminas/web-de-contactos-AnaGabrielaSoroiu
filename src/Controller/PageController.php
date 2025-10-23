<?php

namespace App\Controller;

use App\Entity\Contacto;
use App\Entity\Provincia;
use App\Form\ContactoFormType as ContactoType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ContactoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController {
    private $contactos = [
        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]
    ];
    
    #[Route('/index', name: 'index')]
    public function index() : Response {
        return $this->render('login/inicio.html.twig');
    }


    //Al haberle puesto una provincia, ya nos dará error todo el rato
     #[Route('/contacto/insertar', name: 'insertar')]
        public function insertar(ManagerRegistry $doctrine) {
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $doctrine->getManager();
        foreach($this->contactos as $c){
            $contacto = new Contacto();
            $contacto->setNombre($c["nombre"]);
            $contacto->setTelefono($c["telefono"]);
            $contacto->setEmail($c["email"]);
            $entityManager->persist($contacto);
        } 
        try {
            $entityManager->flush();
            return new Response("Contactos insertados");
        } catch (\Exception $e) {
            return new Response("Error insertando objetos");
        }
    }

    #[Route('/', name: 'inicio')]
    public function inicio(ManagerRegistry $doctrine): Response {
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $repositorio = $doctrine->getRepository(Contacto::class);
        $contactos = $repositorio->findAll();
        
        return $this->render('inicio.html.twig', 
        ['contactos' => $contactos]);
    }

    #[Route('/contacto/{codigo?1}', name: 'ficha_contacto',
     requirements: ['codigo' => '\d+'])]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response {
        //return new Response("Datos del contacto con código $codigo");
        /*$resultado = ($this->contactos[$codigo] ?? null);

        if($resultado) {
            $html = "<ul>";
                $html .= "<li>$codigo</li>";
                $html .= "<li>" . $resultado['nombre'] . "</li>";
                $html .= "<li>" . $resultado['telefono'] . "</li>";
                $html .= "<li>" . $resultado['email'] . "</li>";
            $html .= "</ul>";
            return new Response("<html><body>$html</body>");
        }
        return new Response("<html><body>Contacto $codigo no encontrado</body>");
        $resultado = ($this->contactos[$codigo] ?? null);
        return $this->render('ficha_contacto.html.twig', ['contacto' => $resultado]);*/

        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

        return $this->render('ficha_contacto.html.twig', 
        ['contacto' => $contacto]);
    }

    #[Route('/contacto/buscar/{texto}', name: 'buscar_contacto')]
    public function buscar(ContactoRepository $repositorio, $texto): Response{
        $contactos = $repositorio->findByName($texto);

        return $this->render('lista_contacto.html.twig', 
        ['contactos' => $contactos]);
    }

    #[Route('/contacto/update/{telefono}/{nombre}', name: 'modificar_contacto')]
    public function update(ManagerRegistry $doctrine, $nombre, $telefono): Response {
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($nombre); 
        if($contacto) {
            $contacto->setTelefono($telefono);   
            try {
                $entityManager->flush();
                return $this->render(('ficha_contacto.html.twig'), 
                    ['contacto' => $contacto]);
            } catch (\Exception $e) {
                return new Response("Error insertando objetos");
            }  
        } else {
            return $this->render('ficha_contacto.html.twig', 
            ['contacto' => null]);
        }
     
    }

    #[Route('/contacto/delete/{codigo}', name: 'eliminar_contacto')]
    public function delete(ManagerRegistry $doctrine, $codigo): Response{
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);
        if($contacto){
            try{
                $entityManager->remove($contacto);
                $entityManager->flush();
                return new Response("Contacto eliminado");
            } catch (\Exception $e) {
                return new Response("Error eliminando objeto");
            } 
        } else {
            return $this->render('ficha_contacto.html.twig', 
            ['contacto' => null]);
        }
    }

    #[Route('/contacto/insertarConProvincia', name: 'insertar_provincia')]
    public function insertarProvincia(ManagerRegistry $doctrine): Response{
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        
        $entityManager = $doctrine->getManager();
        $provincia = new Provincia();
        $provincia->setNombre("Valencia");
        
        $contacto = new Contacto();
        $contacto->setNombre("Sara, Fatima Sara");
        $contacto->setTelefono("10101010100");
        $contacto->setEmail("sara@sara.es");
        $contacto->setProvincia($provincia);

        $entityManager->persist($provincia);
        $entityManager->persist($contacto);

        $entityManager->flush();
        return $this->render('ficha_contacto.html.twig',
        ['contacto' => $contacto]);
    }

    #[Route('/contacto/insertarSinProvincia', name: 'insertar_provincia')]
    public function insertarSinProvincia(ManagerRegistry $doctrine): Response{
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Provincia::class);

        $provincia = $repositorio->findOneBy(["nombre" => "Valencia"]);
        
        $contacto = new Contacto();
        $contacto->setNombre("Sara, Fatima Sara sin provincia");
        $contacto->setTelefono("1010101010");
        $contacto->setEmail("sara@sarasin.es");
        $contacto->setProvincia($provincia);

        $entityManager->persist($contacto);

        $entityManager->flush();
        return $this->render('ficha_contacto.html.twig',
        ['contacto' => $contacto]);
    }

    #[Route('/contacto/nuevo', name: 'nuevo')]
    public function nuevo(ManagerRegistry $doctrine, Request $request) {
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        
        $contacto = new Contacto();
        $formulario = $this->createForm(ContactoType::class, $contacto);
        $formulario-> handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $contacto = $formulario->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($contacto);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_contacto', 
            ["codigo" => $contacto->getId()]);
        }
        return $this->render('nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }

    #[Route('/contacto/editar/{codigo}', name: 'editar', requirements:["codigo" => "\d+"])]
    public function editar(ManagerRegistry $doctrine, Request $request, int $codigo) {
        //Si no está logeado, le manda a login/inicio
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);
        if ($contacto) {
            $formulario = $this->createForm(ContactoType::class, $contacto);
            $formulario->handleRequest($request);
            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $contacto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_contacto', 
                ["codigo" => $contacto->getId()]);
            }
            return $this->render('nuevo.html.twig', 
            array('formulario' => $formulario->createView()));
        } else {
            return $this->render('ficha_contacto.html.twig',
            ['contacto' => NULL]);
        }
        
    }

}
 

