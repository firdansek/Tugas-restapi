<?php
header("Content-Type: application/json");
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $koneksi->prepare("SELECT * FROM buku WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Data tidak ditemukan']);
            }
        } else {
            $sql = "SELECT * FROM buku";
            $result = $koneksi->query($sql);
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        $judul = $input['judul'];
        $penulis = $input['penulis'];
        $tahun = $input['tahun_terbit'];

        $stmt = $koneksi->prepare("INSERT INTO buku (judul, penulis, tahun_terbit) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $judul, $penulis, $tahun);
        $status = $stmt->execute();

        echo json_encode(['status' => $status]);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input['id'];
        $judul = $input['judul'];
        $penulis = $input['penulis'];
        $tahun = $input['tahun_terbit'];

        $stmt = $koneksi->prepare("UPDATE buku SET judul=?, penulis=?, tahun_terbit=? WHERE id=?");
        $stmt->bind_param("sssi", $judul, $penulis, $tahun, $id);
        $status = $stmt->execute();

        echo json_encode(['status' => $status]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input['id'];

        $stmt = $koneksi->prepare("DELETE FROM buku WHERE id=?");
        $stmt->bind_param("i", $id);
        $status = $stmt->execute();

        echo json_encode(['status' => $status]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
?>
