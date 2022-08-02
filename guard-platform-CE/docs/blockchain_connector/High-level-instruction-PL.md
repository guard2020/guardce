# Kroki do uruchomienia
1. Trzeba wystawić cztery centra autoryzacji:
- dwa TLS CA's (służące do wystawiania certyfikatów TLS) jeden dla peer'ów i drugi dla orderer'ów.
- dwa Root CA's (służące do wystawiania certyfikatów tożsamości) jeden dla peer'ów i drugi dla orderer'ów.

2. Pobieramy certyfikaty wszystkich utworzonych CA's.

3. Musimy dokonać enrollmentu użytkowników dla wszystkich utworzonych CA (pobranie materiałów kryptograficznych potrzebnych do identyfikacji użytkownika) - CA wymaga autoryzacji requestów.

4. W każdym CA musimy zarejestrować użytkowników:
- dla TLS CA i RCA Ordererów rejestrujemy każdego orderera (orderer1, orderer2, orderer3) oraz admin dla orderer'ów (potrzebny do interakcji z ordererami)
- dla TLS CA i RCA Peerów rejestrujemy każdego peera (tj. peer1, peer2, peer3), admin dla peer'ów (potrzebny do interakcji z peerami) oraz użytkownika dla api (potrzebny dla blockchain connector).

5. Dokonujemy enrollemntu zarejestrowanych użytkowników, żeby pobrać potrzebne materiały kryptograficzne.

6. Na podstawie pobranych danych generujemy genesis.block dla kanału systemowego oraz transakcję służącą do utworzenia nowego kanału - na którym będzie znajdował się chaincode.

7. Używając pobranych materiały kryptograficznych uruchamiamy ordererów.

8. Używając pobranych materiały kryptograficznych uruchamiamy peery.

8. Przy pomocy CLI peer'ów tworzymy kanał i dołączamy do niego pozostałe peery.

9. budujemy i przygotowujemy packaing dla chaincode

10. Przy pomocy CLI peer'ów instalujemy packaing na wszystkich peerach. 

11. Uruchamiamy kontener z chaincode, podając id zainstalowanego packaing.

12. Przy pomocy CLI peer'ów aprobujemy i inicjujemy chaincode.

13. Wszystkie CA mogą zostać już wyłączone. Są one potrzebna tylko wtedy kiedy będziemy wchodzić z nimi w interakcje np. rejestrując lub enrollując użytkowników.

14. uruchamiamy blockchain connector

W przypadku potrzeby modyfikacji konfiguracji kanału używać będziemy konta admina dla ordererów. W przypadku dodawania nowego peer'a lub orderer'a musimy dla niego zarejestrować użytkownika i wygenerować certyfikaty TLS (przy pomocy TLS CA) i tożsamości (RCA). Następnie musimy pobrać aktualną konfigurację kanału, który chcemy zmodyfikować. Ręcznie modyfikujemy konfigurację na taką jaką chcemy uzyskać oraz tworzymy transakcję aktualizującą kanał (przy pomocy orderer-cli)
