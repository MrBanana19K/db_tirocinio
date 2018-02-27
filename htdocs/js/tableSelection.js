class TableSelection
{
	/**
	 *
	 * @param tbody jQuery
	 */
	constructor(tbody)
	{
		this.selected_row = null;
		this.tbody = tbody;

		this.tbody.on("click", "tr", this.onClick.bind(this));
		this.tbody.on("keyup", "tr", this.onClick.bind(this));

		this.handlers = [];
	}

	/**
	 *
	 * @returns {null, jQuery}
	 */
	getSelectedRow()
	{
		return this.selected_row;
	}

	/**
	 * @private
	 * @param event
	 */
	onClick(event)
	{
		let code = event.which;
		if (code !== 1 && code !== 32 && code !== 13 && code !== 188 && code !== 186)
			return;

		if(this.selected_row !== null)
			this.selected_row.removeClass("is-selected");

		if(this.selected_row === null || event.currentTarget !== this.selected_row[0])
		{
			$ (event.currentTarget).addClass ("is-selected");
			this.selected_row = $(event.currentTarget);
		}
		else
			this.selected_row = null;

		for(let i = 0; i < this.handlers.length; i++)
			this.handlers[i](this.selected_row);
	}

	/**
	 * La funzione passata come argomento verra chiamataogni qualcova una line viene
	 * selezionata ovvero deleselezionata!
	 * @param handler (row). Il primo argomento è il riferimo alla TR selezionata,
	 * null viene passato se nessuna riga è selezionata!
	 */
	addHandler(handler)
	{
		this.handlers.push(handler);
	}
}