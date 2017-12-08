<?php
function GetSignedData( $qrystr, &$data, &$sig ) {          // renvoi les donnes signees et la signature

    $pos = strrpos( $qrystr, '&' );                         // cherche dernier separateur
    $data = substr( $qrystr, 0, $pos );                     // et voila les donnees signees
    $pos= strpos( $qrystr, '=', $pos ) + 1;                 // cherche debut valeur signature
    $sig = substr( $qrystr, $pos );                         // et voila la signature
    $sig = base64_decode( urldecode( $sig ));               // decodage signature
}

function LoadKey( $keyfile, $pub=true, $pass='' ) {         // chargement de la clé (publique par défaut)

    $fp = $filedata = $key = FALSE;                         // initialisation variables
    $fsize =  filesize( $keyfile );                         // taille du fichier
    if( !$fsize ) return FALSE;                             // si erreur on quitte de suite
    $fp = fopen( $keyfile, 'r' );                           // ouverture fichier
    if( !$fp ) return FALSE;                                // si erreur ouverture on quitte
    $filedata = fread( $fp, $fsize );                       // lecture contenu fichier
    fclose( $fp );                                          // fermeture fichier
    if( !$filedata ) return FALSE;                          // si erreur lecture, on quitte
    if( $pub )
        $key = openssl_pkey_get_public( $filedata );        // recuperation de la cle publique
    else                                                    // ou recuperation de la cle privee
        $key = openssl_pkey_get_private( array( $filedata, $pass ));
    return $key;                                            // renvoi cle ( ou erreur )
}
function PbxVerSign( $qrystr, $keyfile ) {                  // verification signature Paybox

    $key = LoadKey( $keyfile );                             // chargement de la cle
    if( !$key ) return -1;                                  // si erreur chargement cle
//  penser à openssl_error_string() pour diagnostic openssl si erreur
    GetSignedData( $qrystr, $data, $sig );                  // separation et recuperation signature et donnees
    return openssl_verify( $data, $sig, $key );             // verification : 1 si valide, 0 si invalide, -1 si erreur
}


?>