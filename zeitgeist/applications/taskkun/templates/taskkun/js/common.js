
function showTooltip(text, event)
{
	document.getElementById('Tooltip').innerHTML = text;

	var topPixel = event.clientY + 10;
	var leftPixel = event.clientX + 10;

	x = (document.all) ? window.event.x + document.body.scrollLeft + 180: event.pageX;
	y = (document.all) ? window.event.y : event.pageY;
	document.getElementById('Tooltip').style.left = (x + 20) + "px";
	document.getElementById('Tooltip').style.top = (y + 20) + "px";
	document.getElementById('Tooltip').style.display = "block";
}

function hideTooltip()
{
	document.getElementById('Tooltip').innerHTML = "";

	document.getElementById('Tooltip').style.top = "0px";
	document.getElementById('Tooltip').style.left = "0px";
	document.getElementById('Tooltip').style.display = "none";
}
