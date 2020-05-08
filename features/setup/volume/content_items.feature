Feature: Create Content Items used for volue testing

  @admin @contentItems
  Scenario: Create a language, Content Type and Content Items
    Given I create "Folder" Content items in root in "eng-GB"
      | name   | short_name  |
      | Europe | Europe      |
    And I create "Folder" Content items in "Europe" in "eng-GB"
      | name        | short_name  |
      | France      | France      |
      | Poland      | Poland      |
      | Germany     | Germany     |
      | England     | England     |
      | Italy       | Italy       |
      | Spain       | Spain       |
      | Portugal    | Portugal    |
      | Malta       | Malta       |
      | Austria     | Austria     |
      | Switzerland | Switzerland |
    And I create "Folder" Content items in "Europe/France" in "eng-GB"
      | name        | short_name  |
      | Paris       | Paris       |
      | Bordeaux    | Bordeaux    |
      | Marseille   | Marseille   |
      | Nantes      | Nantes      |
      | Lyon        | Lyon        |
      | Toulouse    | Toulouse    |
      | Nice        | Nice        |
      | Montpellier | Montpellier |
      | Strasbourg  | Strasbourg  |
      | Lille       | Lille       |
    And I create "Folder" Content items in "Europe/Poland" in "eng-GB"
      | name      | short_name |
      | Katowice  | Katowice   |
      | Warszawa  | Warszawa   |
      | Krakow    | Krakow     |
      | Gdansk    | Gdansk     |
      | Wroclaw   | Wroclaw    |
      | Poznan    | Poznan     |
      | Lublin    | Lubin      |
      | Zabrze    | Zabrze     |
      | Gliwice   | Gliwice    |
      | Bialystok | Bialystok  |
    And I create "Folder" Content items in "Europe/Germany" in "eng-GB"
      | name       | short_name |
      | Berlin     | Berlin     |
      | Munich     | Munich     |
      | Frankfurt  | Frankfurt  |
      | Nuremberg  | Nuremberg  |
      | Stuttgart  | Stuttgart  |
      | Hamburg    | Hamburg    |
      | Cologne    | Cologne    |
      | Dusseldorf | Dusseldorf |
      | Dortmund   | Dortmund   |
      | Essen      | Essen      |
    And I create "Folder" Content items in "Europe/England" in "eng-GB"
      | name       | short_name |
      | Birmingham | Birmingham |
      | Bristol    | Bristol    |
      | Cambridge  | Cambridge  |
      | Liverpool  | Liverpool  |
      | London     | London     |
      | Leeds      | Leeds      |
      | Cornwall   | Cornwall   |
      | Manchester | Manchester |
      | Bradford   | Bradford   |
      | Durham     | Durham     |
    And I create "Folder" Content items in "Europe/Italy" in "eng-GB"
      | name       | short_name |
      | Rome       | Rome       |
      | Turin      | Turin      |
      | Milan      | Milan      |
      | Naples     | Naples     |
      | Palermo    | Palermo    |
      | Genoa      | Genoa      |
      | Bologna    | Bologna    |
      | Florence   | Florence   |
      | Bari       | Bari       |
      | Venice     | Venice     |
    And I create "Folder" Content items in "Europe/Spain" in "eng-GB"
      | name       | short_name |
      | Madrid     | Madrid     |
      | Barcelona  | Barcelona  |
      | Valencia   | Valencia   |
      | Sevilla    | Sevilla    |
      | Zaragoza   | Zaragoza   |
      | Malaga     | Malaga     |
      | Murcia     | Murcia     |
      | Palma      | Palma      |
      | Bilbao     | Bilbao     |
      | Cordoba    | Cordoba    |
    And I create "Folder" Content items in "Europe/Malta" in "eng-GB"
      | name       | short_name |
      | Mdina      | Mdina      |
      | Qormi      | Qormi      |
      | Rabat      | Rabat      |
      | Valletta   | Valletta   |
      | Birgu      | Birgu      |
      | Bormla     | Bormla     |
      | Senglea    | Senglea    |
      | Siggiewi   | Siggiewi   |
      | Zabbar     | Zabbar     |
      | Zebbug     | Zebbug     |
    And I create "Folder" Content items in "Europe/Austria" in "eng-GB"
      | name       | short_name |
      | Vienna     | Vienna     |
      | Graz       | Graz       |
      | Linz       | Linz       |
      | Salzburg   | Salzburg   |
      | Innsbruck  | Innsbruck  |
      | Klagenfurt | Klagenfurt |
      | Bregenz    | Bregenz    |
      | Eisenstadt | Eisenstadt |
      | Villach    | Villach    |
      | Retz       | Retz       |
    And I create "Folder" Content items in "Europe/Switzerland" in "eng-GB"
      | name       | short_name |
      | Zurich     | Zurich     |
      | Geneva     | Geneva     |
      | Basel      | Basel      |
      | Bern       | Bern       |
      | Lugano     | Lugano     |
      | Sion       | Sion       |
      | Uster      | Uster      |
      | Vernier    | Vernier    |
      | Chur       | Chur       |
      | Thun       | Thun       |
    And I create "Folder" Content items in "Europe/Portugal" in "por-PT"
      | name       | short_name |
      | Lisbon     | Lisbon     |
      | Porto      | Porto      |
      | Amadora    | Amadora    |
      | Braga      | Braga      |
      | Coimbra    | Coimbra    |
      | Funchal    | Funchal    |
      | Almada     | Almada     |
      | Setubal    | Setubal    |
      | Queluz     | Queluz     |
      | Viseu      | Viseu      |
