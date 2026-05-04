document.getElementById("formRevenu").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("insert.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("message").innerText = data;
    })
    .catch(error => {
        console.error("Erreur :", error);
    });
});