<?php

/**
 * Created by Rogerio Kleinkauf.
 * Date: 14/10/2019
 * Time: 09:11
 */

class AEX{

    var $publicKey;
    var $privateKey;
    var $token;
    var $url;

    function __construct(){
        $this->publicKey = '';
        $this->privateKey = '';
        $this->url = "http://45.55.48.173/api/v1/";             // development
        //$this->url = "http://www.aex.com.py/api/v1/";         // production
    }

    /**
     * @return String - codigo_autorizacion
     */
    public function getAuthorization(){
        $session = session_id();
        $hash = md5($this->privateKey.$session);

        $request = array(
            "clave_publica" => $this->publicKey,
            "clave_privada" => $hash,
            "codigo_sesion" => $session
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "autorizacion-acceso/generar",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp->codigo_autorizacion;
    }

    /**
     * @return json - Array - Recupera la lista de las ciudades con sus códigos para los cuales se tiene cobertura.
     */
    public function getCities(){
        $request = array(
            "clave_publica" => $this->publicKey,
            "codigo_autorizacion" => $this->getAuthorization()
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "envios/ciudades",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp;
    }

    /**
     * @param mixed $origin - String - Código de la ciudad de origen
     * @param mixed $destination - String - Clave Código de la ciudad de destino
     * @param mixed $package - Array - Lista de paquetes a enviar
     * @param mixed $type - String - Código que indica el tipo de carga a enviar los valores pueden ser:
     *               - P (Paquete) – Predeterminado.
     *               - D (Documento / Sobre)
     * @return json - Array
     */
    public function getRates($origin,$destination,$package,$type){
        $request = array(
            "clave_publica" => $this->publicKey,
            "codigo_autorizacion" => $this->getAuthorization(),
            "origen" => $origin,
            "destino" => $destination,
            "paquetes" => $package,
            "codigo_tipo_carga" => $type
        );

        print_r($request);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "envios/calcular",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp;
    }

    /**
     * @param mixed $origin - String - Código de la ciudad de origen
     * @param mixed $destination - String - Clave Código de la ciudad de destino
     * @param mixed $operationCode - String - Identificador de la operación ocurrida en el portal del comercio (Id Pedido).
     * @param mixed $package - Array - Lista de paquetes a enviar
     * @param mixed $type - String - Código que indica el tipo de carga a enviar los valores pueden ser:
     *               - P (Paquete) – Predeterminado.
     *               - D (Documento / Sobre)
     * @return json
     */
    public function requestService($origin,$destination,$operationCode,$package,$type){
        $request = array(
            "clave_publica" => $this->publicKey,
            "codigo_autorizacion" => $this->getAuthorization(),
            "origen" => $origin,
            "destino" => $destination,
            "codigo_operacion" => $operationCode,
            "paquetes" => $package,
            "codigo_tipo_carga" => $type
        );

        print_r($request);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "envios/solicitar_servicio",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp;
    }

    /**
     * TODO
     * @param mixed $id - Integer - Identificador de la solicitud de servicio generada. Este valor es devuelto en el método solicitar_servicio
     * @param mixed $idService - Integer - Identificador del tipo de servicio seleccionado. El mismo es devuelto en cada condición servicio devuelta en el método solicitar_servicio
     * @param mixed $pickup - Array - Datos del retiro a realizar (pickup)
     * @param mixed $recipient - Array - Datos del destinatario
     * @param mixed $delivery - Array - Datos de la entrega.
     * @return json
     */
    public function confirmService($id,$idService,$pickup,$recipient,$delivery){
        $request = array(
            "clave_publica" => $this->publicKey,
            "codigo_autorizacion" => $this->getAuthorization(),
            "id_solicitud" => $id,
            "id_tipo_servicio" => $idService,
            "pickup" => $pickup,
            "destinatario" => $recipient,
            "entrega" => $delivery
        );

        print_r($request);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "envios/confirmar_servicio",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp;
    }

    /**
     * @param mixed $trackingCode - Integer - Número de la guía a consultar.
     * @param mixed $operationCode - String - Identificador de la operación ocurrida en el portal del comercio (Id Pedido)
     * @return json
     */
    public function tracking($trackingCode,$operationCode){
        $request = array(
            "clave_publica" => $this->publicKey,
            "codigo_autorizacion" => $this->getAuthorization(),
            "numero_guia" => $trackingCode,
            "codigo_operacion" => $operationCode
        );

        print_r($request);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "envios/tracking",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp;
    }

    /**
     * @param mixed $products - Array - Lista de códigos de productos para los cuales se desea conocer la existencia
     * @return json
     */
    public function getInventory($products){
        $request = array(
            "clave_publica" => $this->publicKey,
            "codigo_autorizacion" => $this->getAuthorization(),
            "codigos_producto" => $products
        );

        print_r($request);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "inventario/existencia",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($request)
        ));

        $resp = curl_exec($curl);
        $resp = json_decode($resp);

        return $resp;
    }
}
