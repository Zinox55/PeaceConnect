<?php
require_once '../model/EventModel.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

try {
    $model = new EventModel();
    
    switch ($action) {
        case 'get_all':
            $events = $model->getAllEvents();
            echo json_encode(['success' => true, 'data' => $events]);
            break;
            
        case 'get_upcoming':
            $events = $model->getUpcomingEvents();
            echo json_encode(['success' => true, 'data' => $events]);
            break;
            
        case 'get_by_id':
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            $event = $model->getEventById($id);
            if ($event) {
                echo json_encode(['success' => true, 'data' => $event]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Événement non trouvé']);
            }
            break;
            
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $titre = trim($_POST['titre'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $date_event = trim($_POST['date_event'] ?? '');
                $lieu = trim($_POST['lieu'] ?? '');
                $image = $_POST['image'] ?? null;
                
                if (empty($titre) || empty($date_event) || empty($lieu)) {
                    echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.']);
                    break;
                }
                
                $result = $model->createEvent($titre, $description, $date_event, $lieu, $image);
                echo json_encode(['success' => $result, 'message' => $result ? 'Événement créé avec succès' : 'Erreur lors de la création']);
            }
            break;
            
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? 0;
                $titre = trim($_POST['titre'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $date_event = trim($_POST['date_event'] ?? '');
                $lieu = trim($_POST['lieu'] ?? '');
                $image = $_POST['image'] ?? null;
                
                $result = $model->updateEvent($id, $titre, $description, $date_event, $lieu, $image);
                echo json_encode(['success' => $result, 'message' => $result ? 'Événement mis à jour avec succès' : 'Erreur lors de la mise à jour']);
            }
            break;
            
        case 'delete':
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            $result = $model->deleteEvent($id);
            echo json_encode(['success' => $result, 'message' => $result ? 'Événement supprimé avec succès' : 'Erreur lors de la suppression']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>