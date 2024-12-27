<?php
require_once '../admin/koneksi.php';

if(isset($_GET['id_poli'])) {
    $id_poli = $_GET['id_poli'];
    
    $sql = "SELECT jp.id, d.nama AS nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai 
            FROM jadwal_periksa jp
            JOIN dokter d ON jp.id_dokter = d.id
            WHERE d.id_poli = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_poli);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $jadwal = array();
    while($row = $result->fetch_assoc()) {
        $jadwal[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($jadwal);
}
?>