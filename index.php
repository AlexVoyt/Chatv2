<?php

    function DisplayMessage($Message)
    {
        printf("%s: %s: %s", $Message["MsgDate"],
                             $Message["AuthorLogin"],
                             $Message["MsgText"]);
        echo "<br>";
    }
    
    try
    {
        $Database = new PDO('mysql:host=localhost;dbname=chatv2', 'alex', 'password');
    }
    catch (PDOException $e)
    {
        printf("ERROR: %s", $e->getMessage());
        die();
    }    
   
    $ShouldLogin = false;
    $ProvidedLogin = $_GET["Login"];
    $ProvidedPassword = $_GET["Password"];
    $ProvidedMessage = $_GET["Message"];
    
    try
    {
        foreach($Database->query('SELECT * FROM Messages') as $Message)
        {
            DisplayMessage($Message);
        }
    }
    catch (PDOException $e)
    {
        printf("ERROR: %s", $e->getMessage());
        die();
    }   

    $GetUserQuery = "Select * From Users Where Login = ? AND Password = ?";
    $GetUserStatement = $Database->prepare($GetUserQuery);
    $GetUserStatement->execute(array($ProvidedLogin, $ProvidedPassword));
    $User = $GetUserStatement->fetchAll();
    if(!empty($User))
    {
        $ShouldLogin = true;
    }
    //print_r($ShouldLogin);
    if($ShouldLogin)
    {
        if(!empty($ProvidedMessage))
        {
            $Date = date("Y-m-d");
            printf("%s: %s: %s", $Date,
                                 $ProvidedLogin,
                                 $ProvidedMessage);
            echo "<br>";

            $InsertMessageQuery = "Insert Into Messages(AuthorLogin, MsgDate, MsgText) Values(?, ?, ?)";
            $InsertMessageStatement = $Database->prepare($InsertMessageQuery);
            $InsertMessageStatement->execute(array($ProvidedLogin, $Date, $ProvidedMessage));
        }
    }
    else
    {
        echo "<br>Wrong login or password";
    }
    

?>