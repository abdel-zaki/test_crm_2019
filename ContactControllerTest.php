<?php

namespace App;

use PHPUnit\Framework\TestCase;
use App\Controllers\ContactController;
use App\Models\UserModel as User;
use App\Models\ContactModel as Contact;
use App;

class ContactControllerTest extends TestCase
{
    /**
     * @var array
     * Variables globales
     */
    protected $backupGlobalsBlacklist = array('_SESSION', '_POST', '_SERVER');
    /**
     * @var string
     * $HTTP_HOST à remplacer par la valeur appropriée de l'hôte
     */
    protected $HTTP_HOST = 'http://leboncoin.local/';
    protected static $userModel;
    protected static $contactModel;
    protected $contactId;
    /**
     * @var array
     * Données de test pour la méthode add()
     */
    protected $dataTestAdd = [
        'nom'       => 'test-add-nom',
        'prenom'    => 'test-add-prenom',
        'email'     => 'test-add@email.com'
    ];
    /**
     * @var array
     * Données de test pour la méthode edit()
     */
    protected $dataTestEdit = [
        'nom'       => 'test-edit-nom',
        'prenom'    => 'test-edit-prenom',
        'email'     => 'test-edit@email.com'
    ];

    /**
     * Charger l'Autoloader, la base de données et les modèles
     */
    public static function setUpBeforeClass()
    {
        define('ROOT', dirname(__DIR__ . '/src'));
        require ROOT . '/app/App.php';
        require ROOT . '/app/Autoloader.php';
        Autoloader::register();
        require ROOT . '/vendor/autoload.php';
        $db = App::getInstance()->getDatabase();
        ContactControllerTest::$userModel = new User($db);
        ContactControllerTest::$contactModel = new Contact($db);
    }

    /**
     * @test
     * Tester l'affichage des contacts par index()
     */
    public function testIndex()
    {
        // Récupérer l'ID de l'utilisateur (admin : lebonoin@test.fr)
        $userRes = get_object_vars(ContactControllerTest::$userModel->findIdByLogin('admin'));
        $userId = $userRes['id'];

        // Faire en sorte que l'utilisateur (admin : lebonoin@test.fr) est authentifié
        $_SESSION['auth']['id'] = $userId;

        // Récupérer le HTML de la page
        $page = $this->getHtmlPage('index');

        // Vérifier si le titre <h4> est "Liste des contacts"
        $h4 = $page->getElementsByTagName('h4');
        $this->assertEquals(1, $h4->length);
        $title = $h4->item(0)->nodeValue;
        $this->assertEquals('Liste des contacts', $title);

        // Récupérer le nombre de contacts de l'utilisateur (admin : lebonoin@test.fr)
        $contactRes = ContactControllerTest::$contactModel->getContactByUser($userId);
        $contactCount = count($contactRes);

        // Vérifier le nombre de contacts affichés pour l'utilisateur (admin : lebonoin@test.fr)
        $table = $page->getElementsByTagName('table');
        $this->assertEquals(1, $table->length);
        $tbody = $table->item(0)->getElementsByTagName('tbody');
        $this->assertEquals(1, $tbody->length);
        $nbContacts = $tbody->item(0)->getElementsByTagName('tr');
        $this->assertEquals($contactCount, $nbContacts->length);
    }

    /**
     * @test
     * Tester le fonctionnement de la méthode add()
     */
    public function testAdd()
    {
        // Récupérer le HTML de la page
        $page = $this->getHtmlPage('add');

        // Vérifier si le titre <h3> est "Créer un nouveau contact"
        $h3 = $page->getElementsByTagName('h3');
        $this->assertEquals(1, $h3->length);
        $title = $h3->item(0)->nodeValue;
        $this->assertEquals('Créer un nouveau contact', $title);

        // Vérifier si la page contient 3 <input>
        $inputs = $page->getElementsByTagName('input');
        $this->assertEquals(3, $inputs->length);

        // Ajouter un contact et vérifier s'il est bien ajouté
        $td = $this->addContactAndGetLine();
        $this->contactId = $td->item(0)->nodeValue;
        $this->assertEquals(ucfirst(strtolower($this->dataTestAdd['nom'])), $td->item(1)->nodeValue);
        $this->assertEquals(ucfirst(strtolower($this->dataTestAdd['prenom'])), $td->item(2)->nodeValue);
        $this->assertEquals(strtolower($this->dataTestAdd['email']), $td->item(3)->nodeValue);
    }

    /**
     * @test
     * Tester le fonctionnement de la méthode edit()
     */
    public function testEdit()
    {
        // Ajouter d'abord un contact, récupérer son ID, pour le modifier
        $td = $this->addContactAndGetLine();
        $contactId = $td->item(0)->nodeValue;
        $this->contactId = $contactId;

        // Modifier les données du contact
        $_POST = $this->dataTestEdit;
        $this->getHtmlPage('edit', $contactId);

        // Editer le contact afin de vérifier si les modifications sont appliquées
        $_POST = null;
        $page = $this->getHtmlPage('edit', $contactId);
        $nom = $page->getElementById('nom')->getAttribute('value');
        $prenom = $page->getElementById('prenom')->getAttribute('value');
        $email = $page->getElementById('email')->getAttribute('value');

        // Vérifier si le titre <h3> est "Editer un contact"
        $h3 = $page->getElementsByTagName('h3');
        $this->assertEquals(1, $h3->length);
        $title = $h3->item(0)->nodeValue;
        $this->assertEquals('Editer un contact', $title);

        $this->assertEquals(ucfirst(strtolower($this->dataTestEdit['nom'])), $nom);
        $this->assertEquals(ucfirst(strtolower($this->dataTestEdit['prenom'])), $prenom);
        $this->assertEquals(strtolower($this->dataTestEdit['email']), $email);
    }

    /**
     * @test
     * Tester le fonctionnement de la méthode delete()
     */
    public function testDelete()
    {
        // Ajouter d'abord un contact, récupérer son ID, puis le supprimer
        $td = $this->addContactAndGetLine();
        $contactId = $td->item(0)->nodeValue;
        $contact = new ContactController();
        $contact->delete($contactId);

        // Tenter d'éditer le contact récemment supprimé et vérifier s'il y a un message d'erreur
        $page = $this->getHtmlPage('edit', $contactId);
        $messageError = $page->getElementById('message-error');
        $this->assertGreaterThan(0, $messageError->getElementsByTagName('div')->length);
    }

    /**
     * @test
     * Tester le fonctionnement de la méthode sanitize()
     */
    public function testSanitize()
    {
        $data = [
            'nom'      => $this->dataTestAdd['nom'],
            'prenom'   => $this->dataTestAdd['prenom'],
            'email'    => $this->dataTestAdd['email']
        ];
        $contact = new ContactController();
        $_SERVER['HTTP_HOST'] = $this->HTTP_HOST;
        $result = $contact->sanitize($data);
        $this->assertEquals(true, $result['response']);
    }



    /**
     * @return \DOMNodeList
     * Récupérer la dernière ligne dans la liste des contacts
     */
    public function getLastInsertedLine() : \DOMNodeList
    {
        $page = $this->getHtmlPage('index');
        $table = $page->getElementsByTagName('table');
        $tbody = $table->item(0)->getElementsByTagName('tbody');
        $tr = $tbody->item(0)->getElementsByTagName('tr');
        $lastLine = $tr->item($tr->length-1);
        return $lastLine->getElementsByTagName('td');
    }
    /**
     * @param string $methode
     * @param string $param
     * @return \DOMDocument
     */
    public function getHtmlPage(string $methode, $param = null) : \DOMDocument
    {
        $contact = new ContactController();
        if (!$param) {
            $result = $contact->$methode();
        }
        else {
            $result = $contact->$methode($param);
        }
        $page = new \DOMDocument();
        libxml_use_internal_errors(true);
        $page->loadHTML($result);
        libxml_clear_errors();
        return $page;
    }

    /**
     * @return \DOMNodeList
     * Ajouter un contact et récupérer la ligne
     */
    public function addContactAndGetLine()
    {
        // Ajouter un contact
        $_POST = $this->dataTestAdd;
        $_SERVER['HTTP_HOST'] = $this->HTTP_HOST;
        $this->getHtmlPage('add');

        // Récupérer la ligne du contact récemment ajouté
        return $this->getLastInsertedLine();
    }

    /**
     * Réinitialiser les variables globales et supprimer les contacts ajoutés
     */
    public function tearDown()
    {
        $_POST = null;
        $_SERVER['HTTP_HOST'] = NULL;
        if ($this->contactId) {
            $contact = new ContactController();
            $contact->delete($this->contactId);
        }
    }

}
