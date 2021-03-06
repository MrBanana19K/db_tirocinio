$.fn.isOnScreen = function ()
{
	let element = this.get (0);
	let bounds = element.getBoundingClientRect ();
	return bounds.top < window.innerHeight && bounds.bottom > 0;
};

let index = 0;
let semaforo = false;
let selezione = parseInt($.urlParam.get("time"));

if(selezione === undefined || isNaN(selezione))
	selezione = 1;
else
{
	let btns = $ ('.switch');
	btns.removeClass ("is-active");
	btns.filter("[data-selezione=\"" +  selezione + "\"]").addClass("is-active");
}

if(typeof (docente) == "undefined")
	docente = undefined;

if(typeof (studente) == "undefined")
	studente = undefined;


window.setInterval (function ()
{
	if (semaforo)
		return;

	// Notare che quando il div diventerà HIDDEN la condizione sarà sempre false!
	if ($ ("#loading_go_on").isOnScreen ())
	{
		semaforo = true;
		$.get (
			"tirocinio.php",
			{
				index: index++,
				chTrain: selezione,
				/**
				 * È undefined eccuttato nella pagina dei docenti!
				 * @see ../pages/docente/tirocini/list/js/tirocini_filter.js
				 */
				docente: docente,
				studente: studente
			}
		).done (function (data)
		{
			if (data.length === 0)
			{
				$ ("#loading_go_on").hide ();
				$ ("#loading_stop").show ();
			}
			else
				$ ("#tirocinis").append (data);
		}).always (function ()
		{
			semaforo = false;
		});
	}
}, 250);

$(".switch").on("keyup", function (e)
{
	if(e.which === 13)
		$(this).click();
});

$ ('.switch').on ('click', function ()
{
	if ($ (this).hasClass ("is-active"))
		return;

	if (semaforo)
		return;

	semaforo = true;

	selezione = $ (this).data ("selezione");

	$ ("#loading_go_on").show ();
	$ ("#loading_stop").hide ();

	$ ("#tirocinis").html ("");

	$ ('.switch').removeClass ("is-active");
	$ (this).addClass ("is-active");

	// Modifico la cronologia se il browser lo supporta
	$.urlParam.set("time", selezione);

	index = 0;
	semaforo = false;
});
