<?php
 /*
  * Consultas a servicios generales que responde la API
  */

  namespace Controller;
  

  use Silex\Application;
  use Silex\Api\ControllerProviderInterface;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Doctrine\Common\ClassLoader;
  use Symfony\Component\HttpFoundation\ParameterBag;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\RedirectResponse;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
  
  class InfoController implements ControllerProviderInterface {
	  
	  public function connect(Application $app) {
        $indexController = $app['controllers_factory'];
        $indexController->get("/v1/info/servicios", array($this, 'servicios'))->bind('info_servicios');
        $indexController->post("/v1/info/notificar", array($this, 'notificar'))->bind('info_notificar');
        
        
        $indexController->get("/", array($this, 'index'))->bind('acme_index');
        $indexController->get("/show/{id}", array($this, 'show'))->bind('acme_show');
        $indexController->match("/create", array($this, 'create'))->bind('acme_create');
        $indexController->match("/update/{id}", array($this, 'update'))->bind('acme_update');
        $indexController->get("/delete/{id}", array($this, 'delete'))->bind('acme_delete');
        return $indexController;
    }
    
    public function servicios(Application $app) {
		$array['respuesta']=array("info", "establecimientos", "suministros", "consumos");
		$array['codigo'] = 201;
		
		return $app->json(array('respuesta' => $array['respuesta']), $array['codigo']);
    }
    
    public function notificar(Application $app, Request $request) {
		$destino = $request->get("destino");
		$asunto = $request->get("asunto");
		$mensaje = $request->get("mensaje");
		//$adjunto = $_POST["adjunto"];
	 
		$message = \Swift_Message::newInstance()
        ->setSubject("$asunto")
        ->setFrom(array('info@salud.gob.sv'))
        ->setTo(array("$destino"))
        ->setBody("$mensaje");
        
        $app['mailer']->send($message);
        return $app->json(array('respuesta' => 'Mensaje enviado'), 201);
    }
    
  }
