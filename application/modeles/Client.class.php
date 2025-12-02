<?php

class Client {
    private $id;
    private $nom;
    private $prenom;
    private $rue;
    private $codePostal;
    private $ville;
    private $tel;
    private $email;
    private $mdp;

    public function __construct($id, $nom, $prenom, $rue, $codePostal, $ville, $tel, $email, $mdp) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->rue = $rue;
        $this->codePostal = $codePostal;
        $this->ville = $ville;
        $this->tel = $tel;
        $this->email = $email;
        $this->mdp = $mdp;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getRue() { return $this->rue; }
    public function getCodePostal() { return $this->codePostal; }
    public function getVille() { return $this->ville; }
    public function getTel() { return $this->tel; }
    public function getEmail() { return $this->email; }
    public function getMdp() { return $this->mdp; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setRue($rue) { $this->rue = $rue; }
    public function setCodePostal($codePostal) { $this->codePostal = $codePostal; }
    public function setVille($ville) { $this->ville = $ville; }
    public function setTel($tel) { $this->tel = $tel; }
    public function setEmail($email) { $this->email = $email; }
    public function setMdp($mdp) { $this->mdp = $mdp; }
} 