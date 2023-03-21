<?php

require_once "db.php";

$db = Db::getDb();

// CORS
header("Access-Control-Allow-Origin: *");

if (isset($_POST['action'])) {
    if ($_POST['action'] === "create") {
        $tache = "";
        $echeance = NULL;
        if (isset($_POST['tache'])) {
            $tache = htmlentities($_POST['tache']);
        }
        if (isset($_POST['echeance'])) {
            $echeance = htmlentities($_POST['echeance']);
            if ($echeance === "null") {
                $echeance = NULL;
            }
        }
        
        $query = "INSERT INTO todo (tache, echeance) VALUES (:tache, :echeance)";
        $stmt = $db->prepare($query);
        $stmt->execute(['tache' => $tache, 'echeance' => $echeance]);
    }
    
    if ($_POST['action'] === "update") {
        $id = 0;
        $tache = "";
        $echeance = NULL;
        $etat = NULL;
        
        $query = "UPDATE todo SET ";
        $arr = [];
        
        if (isset($_POST['id'])) {
            $id = htmlentities($_POST['id']);
            
            $arr["id"] = $id;
        } else {
            die;
        }
        
        if (isset($_POST['tache'])) {
            $tache = htmlentities($_POST['tache']);
            
            $query .= "tache = :tache";
            $arr["tache"] = $tache;
        }
        if (isset($_POST['echeance'])) {
            $echeance = htmlentities($_POST['echeance']);
            
            if (count($arr) > 1) {
                $query .= ",";
            }
            $query .= "echeance = :echeance";
            $arr["echeance"] = $echeance;
        }
        if (isset($_POST['etat'])) {
            $etat = htmlentities($_POST['etat']);
            
            if (count($arr) > 1) {
                $query .= ",";
            }
            $query .= "etat = :etat";
            $arr["etat"] = $etat;
        }
        
        $query .= " WHERE id = :id";
        $stmt = $db->prepare($query);
        
        echo $query;
        
        $stmt->execute($arr);
    }
    
    if ($_POST['action'] === "delete") {
        $id = 0;
        if (isset($_POST['id'])) {
            $id = htmlentities($_POST['id']);
        } else {
            die;
        }
        
        $query = "DELETE FROM todo WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $id]);
    }
}

if (isset($_GET['action'])) {
    if ($_GET['action'] === "read") {
        $query = "SELECT * FROM todo";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}