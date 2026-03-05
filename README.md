# Integrazione SPID e CIE in Vitaever

Questo repository contiene l'integrazione che consente al portale **Vitaever** di autenticare gli utenti tramite **SPID** e **CIE** utilizzando il protocollo **SAML**.

## Requisiti

Il progetto è stato sviluppato utilizzando:

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.4.10-8892BF.svg)](https://php.net/)

È necessario inoltre che:

- Il progetto sia eseguito su un **dominio con protocollo HTTPS**
- Sia disponibile **Composer** per la gestione delle dipendenze

---

# Installazione

1. Clonare o copiare il progetto sul server.

2. Creare il file `.env` nella root del progetto e configurare i seguenti parametri:
   ``` bash
    CLIENT_ID=.......
    SHARED_SECRET=.......
   ```

3. Esegui il comando `composer install` per installare i pacchetti e avviare la procedura di configurazione automatica.
   Durante la post-installazione, ti verrà richiesto di compilare diversi campi: questi dati sono fondamentali per la generazione automatica dei file XML dei metadati.

# Metadati
Se l'installazione è stata eseguita correttamente, gli XML (Metadata) per SPID e CIE saranno disponibili ai seguenti indirizzi:

SPID: https://sitocheospitailprogetto.com/vitaever/module.php/saml/sp/metadata.php/spid

CIE: https://sitocheospitailprogetto.com/vitaever/module.php/saml/sp/metadata.php/cie
Nota: `/vitaever/` nel percorso corrisponde al nome del servizio impostato durante la fase di post-installazione di Composer.

Info sui files:d
- login_saml.php è l'endpoint dal quale Vitaever fa la richiesta per poi accedere agli IDP sia con CIE che con SPID.
- login_test.php è un file dimostrativo dei bottoni di login, è possibile eseguire il login in questa pagina e recuperare tutti i dati dell'utente loggato. E' possibile anche sloggarsi.
- /vendor/simplesamlphp/simplesamlphp/config/authsources.php è un file che permette di modificare i dati presenti all'interno dell'xml
- /vendor/simplesamlphp/simplesamlphp/cert/* sono presenti i certificati sia di SPID che di CIE. Sostituisci questi file per aggiornare i certificati dell'XML.

