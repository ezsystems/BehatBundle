Feature: Create Content Items used for volue testing

  @admin @contentItems
  Scenario: Create a language, Content Type and Content Items
    Given I create "folder" Content items in root in "eng-GB"
      | name   | short_name  |
      | Europe | Europe      |
    And I create "folder" Content items in "Europe" in "eng-GB"
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
      | Ukraine     | Ukraine     |
      | Sweden      | Sweden      |
      | Norway      | Norway      |
      | Finland     | Finland     |
      | Denmark     | Denmark     |
      | Croatia     | Croatia     |
    And I create "folder" Content items in "Europe/France" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Poland" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Germany" in "eng-GB"
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
    And I create "folder" Content items in "Europe/England" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Italy" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Spain" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Malta" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Austria" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Switzerland" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Portugal" in "eng-GB"
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
    And I create "folder" Content items in "Europe/Ukraine" in "eng-GB"
      | name       | short_name |
      | Kyiv       | Kyiv       |
      | Kharkiv    | Kharkiv    |
      | Odessa     | Odessa     |
      | Dnipro     | Dnipro     |
      | Donetsk    | Donetsk    |
      | Zaporizhia | Zaporizhia |
      | Lviv       | Lviv       |
      | Mykolaiv   | Mykolaiv   |
      | Mariupol   | Mariupol   |
      | Luhansk    | Luhansk    |
    And I create "folder" Content items in "Europe/Sweden" in "eng-GB"
      | name        | short_name  |
      | Stockholm   | Stockholm   |
      | Gothenburg  | Gothenburg  |
      | Norrkoping  | Norrkoping  |
      | Karlskrona  | Karlskrona  |
      | Malmo       | Malmo       |
      | Helsingborg | Helsingborg |
      | Gavle       | Gavle       |
      | Uppsala     | Uppsala     |
      | Lund        | Lund        |
      | Halmstad    | Halmstad    |
    And I create "folder" Content items in "Europe/Norway" in "eng-GB"
      | name         | short_name   |
      | Oslo         | Oslo         |
      | Bergen       | Bergen       |
      | Trondheim    | Trondheim    |
      | Stavanger    | Stavanger    |
      | Kristiansand | Kristiansand |
      | Fredrikstad  | Fredrikstad  |
      | Sandnes      | Sandnes      |
      | Skien        | Skien        |
      | Drammen      | Drammen      |
      | Sarpsborg    | Sarpsborg    |
    And I create "folder" Content items in "Europe/Finland" in "eng-GB"
      | name         | short_name   |
      | Helsinki     | Helsinki     |
      | Espoo        | Espoo        |
      | Tampere 	   | Tampere 	    |
      | Vantaa       | Vantaa       |
      | Oulu         | Oulu         |
      | Turku        | Turku        |
      | Lahti        | Lahti        |
      | Kuopio       | Kuopio       |
      | Pori 	       | Pori 	      |
      | Kouvola      | Kouvola      |
    And I create "folder" Content items in "Europe/Denmark" in "eng-GB"
      | name         | short_name   |
      | Copenhagen   | Copenhagen   |
      | Aarhus       | Aarhus       |
      | Odense 	     | Odense 	    |
      | Aalborg      | Aalborg      |
      | Esbjerg      | Esbjerg      |
      | Randers      | Randers      |
      | Kolding      | Kolding      |
      | Horsens      | Horsens      |
      | Vejle 	     | Vejle 	      |
      | Roskilde     | Roskilde     |
    And I create "folder" Content items in "Europe/Croatia" in "eng-GB"
      | name         | short_name   |
      | Zagreb       | Zagreb       |
      | Split        | Split        |
      | Rijeka 	     | Rijeka 	    |
      | Osijek       | Osijek       |
      | Zadar        | Zadar        |
      | Pula         | Pula         |
      | Karlovac     | Karlovac     |
      | Sisak        | Sisak        |
      | Vinkovci     | Vinkovci     |
      | Dubrovnik    | Dubrovnik    |






