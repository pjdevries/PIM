# Hoe kun je beste Ajax calls toepassen vanuit je eigen maatwerk extensie?

Hoe bouw je een formulier waarbij je via een Ajax call data leest en schrijft naar de database zonder op de frontend je scherm te verversen?

Dit zijn meerdere vragen in één en om die te kunnen beantwoorden is meer informatie nodig:

- Wat is het doel van de AJAX call?
- Wat bedoel je precies met "via een Ajax call data leest en schrijft naar de database"? Is dat één call die beiden doet? Zo ja, hoe moet ik dat dan zien?
- Welke data wil je in de frontend verversen als gevolg van een AJAX call?
- Hoe wordt de AJAX call geïnitieerd? D.m.v. een knop? Wanneer de gebruiker een formulierveld muteert?

## AJAX request

### Initiëren van een AJAX request

- In reactie op een formlierveld event zoals bijvoorbeeld `change`.
- Als gevolg van een button click of iets soortgelijks.
- Interceptie van het formulier `submit` event.

### Versturen van een AJAX request

- [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch) gebruiken.
- 3rd party library gebruiken, zoals bijvoorbeeld [Axios](https://axios-http.com/).

### Verwerking van een AJAX request op de server

- De eenvoudige, legacy methode: [Joomla Ajax Interface (`com_ajax`)](https://docs.joomla.org/Using_Joomla_Ajax_Interface). 
- De nieuwe methode: [Adding an API to a Joomla Component](https://docs.joomla.org/J4.x:Adding_an_API_to_a_Joomla_Component)
  - [Joomla Api Specification](https://docs.joomla.org/Joomla_Api_Specification)
  - [Joomla Web Services API 101 - Tokens, Testing and a Taste Test](https://magazine.joomla.org/all-issues/august-2020/joomla-web-services-api-101-tokens,-testing-and-a-taste-test)
  - Playing with the Joomla Web Services (API)
    - [part 1](https://magazine.joomla.org/all-issues/march-2023/playing-with-the-joomla-api-part-1)
    - [part 2](https://magazine.joomla.org/all-issues/april-2023/playing-with-the-joomla-api-part-2)
    - [part 3](https://magazine.joomla.org/all-issues/may-2023/playing-with-the-joomla-api-part-3)
  - (https://github.com/alexandreelise/Manual/blob/patch-1/docs/general-concept/webservices.md)

### Verwerken van een AJAX response in de browser

Mogelijke hulpmiddelen:

- https://htmx.org/
- https://alpinejs.dev/
- https://github.com/vuejs/petite-vue
- https://svelte.dev/
- https://vuejs.org/
- https://react.dev/
- https://angular.io/
- [and many more...](https://stackdiary.com/front-end-frameworks/)

