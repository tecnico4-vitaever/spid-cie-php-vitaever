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

### Dettagli dei File e Configurazione

| File / Percorso                                                                             | Descrizione e Funzionalità |
|:--------------------------------------------------------------------------------------------| :--- |
| **`login_saml.php`**                                                                        | **Endpoint di Autenticazione**: Il punto di ingresso principale dove Vitaever inoltra le richieste per interfacciarsi con gli Identity Provider (IdP) di SPID e CIE. |
| **`login_test.php`**                                                                        | **Pagina Demo**: File dimostrativo con i bottoni di login. Permette di testare l'accesso, recuperare i dati dell'utente loggato e verificare la procedura di logout. |
| **`vendor/simplesamlphp/simplesamlphp/config/authsources.php`**                             | **Configurazione SAML**: File critico per modificare i dati tecnici e i parametri che verranno inclusi all'interno dei file XML dei metadati. |
| **`vendor/simplesamlphp/simplesamlphp/cert/*`**                                           | **Certificati**: Directory contenente i certificati per SPID e CIE. Sostituisci i file in questa cartella per aggiornare la firma e la validità dei metadati XML. |

### Installazione entità dentro un docker-compose

È possibile integrare questo modulo all'interno del progetto [vitaever-dockers](https://github.com/nethical/vitaever-dockers) (branch localphp7) aggiungendo il seguente servizio al file docker-compose.yml:

``` bash
spidcie:
    build:
    context: .
    dockerfile: Dockerfile-spidcie
    hostname: spidcie
    depends_on:
        - db
    networks:
        - app-network
    ports:
        - "3999:80"
    volumes:
        - ./spid-cie-proxy:/var/www/html:delegated
```

Una volta avviati i container, il servizio sarà raggiungibile all'indirizzo:

http://localhost:3999/login_test.php
