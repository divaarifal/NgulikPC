<?php
class ApiClient {
    private $api_url = "http://localhost/NgulikPC/api_gateway";

    public function get($endpoint, $token = null) {
        $ch = curl_init($this->api_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [];
        if ($token) {
            $headers[] = "Authorization: " . $token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function post($endpoint, $data, $token = null) {
        $ch = curl_init($this->api_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $headers = ['Content-Type: application/json'];
        if ($token) {
            $headers[] = "Authorization: " . $token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
?>
