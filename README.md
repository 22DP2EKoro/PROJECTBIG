# PROJECTBIG
Lai palaistu ExploreRiga lokāli, ir jāveic šādi soļi secīgā kārtībā.
3.1. XAMPP servera uzstādīšana
1.	Lejupielādēt un instalēt XAMPP no oficālās vietnes. Instalācijas procesā pārliecināties, ka ir atzīmēti Apache un MySQL komponenti.
2.	Atvērt XAMPP Control Panel un nospiest pogu Start pie Apache un MySQL moduļiem. Abiem jāparādās zaļš darbības indikators.
3.	Pārlūkprogrammā atvērt adresi http://localhost/phpmyadmin un izveidot datubāzi ar nosaukumu user_auth. Izveidot nepieciešamās tabulas: users, objects, museums, restaurants, place, reviews, place_requests.
4.	Nokopēt projekta mapi back-end uz XAMPP htdocs mapi: C:\xampp\htdocs\explore-riga\. Pārliecināties, ka PHP faili ir pieejami adresē http://localhost/explore-riga/
3.2. Vue.js projekta palaišana
1.	Pārliecināties, ka Node.js ir instalēts, komandrindā izpildot: node --version. Jāparādās versijas numurs, piemēram, v20.11.0.
2.	Atvērt komandrindas logu un pārvietoties uz projekta mapi ar komandu: cd ceļš\uz\exploreriga
3.	Izpildīt komandu npm install, lai instalētu visas projekta atkarības. Šī komanda automātiski lejupielādē Vue.js, Vue Router, Axios un citas nepieciešamās bibliotēkas.
4.	Pēc veiksmīgas instalēšanas izpildīt komandu npm run dev, lai palaistu Vite izstrādes serveri. Konsolē parādīsies adrese — parasti http://localhost:5173. Atvērt šo adresi pārlūkprogrammā.
Svarīgi: sistēmai jādarbojas vienlaicīgi gan XAMPP (Apache un MySQL), gan Vite izstrādes serveris (npm run dev). Ja kāds no tiem ir apstājies, sistēma nedarbosies pilnvērtīgi.
