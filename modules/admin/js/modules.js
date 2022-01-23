function modules() {
	var list = document.getElementById("left");
	DragDrop.makeListContainer( list, 'g1' );
	list.onDragOver = function() { this.style["background"] = "#EFE"; };
	list.onDragOut = function() {this.style["background"] = "none"; };
	
	list = document.getElementById("right");
	DragDrop.makeListContainer( list, 'g1' );
	list.onDragOver = function() { this.style["background"] = "#EFE"; };
	list.onDragOut = function() {this.style["background"] = "none"; };
	
	list = document.getElementById("trash");
	DragDrop.makeListContainer( list, 'g1' );
	list.onDragOver = function() { this.style["background"] = "#FEE"; };
	list.onDragOut = function() {this.style["background"] = "none"; };
	
	var submitbtn= document.getElementById('submit');
	submitbtn.onclick=function() {
		ordre = document.getElementById("ordre");
		ordre.value = DragDrop.serData('g1', null);
	}
}

addToStart(modules);