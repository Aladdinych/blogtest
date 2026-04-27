
function resort(){
	let catsort = document.getElementById('category-sort');
	vsort = catsort.value;
	changeURL('sort',vsort);
	changeURL('page',1);
	window.location.reload();
}
function changeURL(name, value){
    if (history.pushState) {
	var url = new URL(window.location.href);
	const regex = new RegExp(name + '\/.*\/');
	var newUrl=url.href.replace(regex,'')
	newUrl = newUrl + name + '/'+ value + '/';
        history.pushState(null, null, newUrl);
    }
    else {
        console.warn('History API не поддерживается');
    }

}