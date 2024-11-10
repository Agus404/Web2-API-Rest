<?php
class JSONView {
    public function response($data, $status = 200) {
        header("Content-Type: application/json");
        $statusText = $this->_requestStatus($status);
        header("HTTP/1.1 $status $statusText");
		
		if ($status == 200 || $status == 201)
			echo json_encode($data);
		else
			echo json_encode(['message' => $data, 'code' => $status]);
    }

    private function _requestStatus($code) {
        $status = array(
            200 => "OK",
            201 => "Created",
            204 => "No Content",
            400 => "Bad Request",
            401 => "Unauthorized",
            404 => "Not Found",
            500 => "Internal Server Error"
        );
        if(!isset($status[$code])) {
            $code = 500;
        }
        return $status[$code];
    }
}