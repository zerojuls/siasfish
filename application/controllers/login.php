<?php

if (!defined('BASEPATH')) exit('No permitir el acceso directo al script');

class Login extends CI_Controller {

    public function __construct() {
        // para que pueda heredar lo que esta en el controlador padre
        parent::__construct();
        $this->_arr_Sesion = $this->session->userdata('ses_usuario');
        // cargando archivo de idiomas
        // $this->lang->load('generales');
        // $this->lang->load('login');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation'); // cargamos la libreria de validacion de fornmulario
        $this->load->model('login_m'); // cargamos el modelo del login
    }

    public function index() {
        $this->load->view('frontend/login/login_v');
    }
    
    public function isValid() {
        $argData = array(
            'usuario' => $this->input->post('txtUsername'),
            'clave' => $this->input->post('txtPassword')
        );
        //si esta vacio 
        if (empty($argData['usuario']) || empty($argData['clave'])) {
            $argData['sMsgError'] = "Ingrese un Usuario y Contraseña";
            $this->load->view('frontend/login/login_v', $argData);
        } else {
            //logeo satisfactorio
            $sArray = $this->login_m->login($argData);
            if ($sArray["valido"] == 1) {
                //generamos la sesion
                $SesLimite = $this->config->item('sess_expiration');                
                // $this->load->view('frontend/main/main_v');
                $this->_generar_sesion($sArray["infoUsuario"], $SesLimite);
            } else { //error
                $argData['sMsgError'] = "Usuario y contraseña no son válidos";
                $this->load->view('frontend/login/login_v', $argData);
            }
        }
    }
    // generamos la sesion con los datos del usuario
    function _generar_sesion($sArray, $SesLimite) {
        // armamos un array con los datos de la sesion
        $arrSesion = array(
            'id_usuario'            => $sArray['id_usuario'],
            'usuario'               => $sArray['usuario'],
            'fecha_vigencia'        => $sArray['fecha_vigencia'],
            'id_perfil'             => $sArray['id_perfil'],
            'perfil'                => $sArray['perfil'],
            'timeLogin'             => date('Y-m-d H:m:s')
        );
        // solo si se desea usar tiempo limite de sesion personalizado
        if ($this->config->item('sess_use_time_expire')) {
            // lo pasamos a segundos
            $arrSesion['seslimite'] = time() + ($SesLimite * 60);
        } else {
            $arrSesion['seslimite'] = time() + $this->config->item('sess_expiration');
        }
        // se establece la sesion
        $this->session->set_userdata('ses_usuario', $arrSesion);
        redirect('main');
    }
}