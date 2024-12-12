// Écoute l'événement DOMContentLoaded pour s'assurer que le DOM est entièrement chargé avant d'exécuter le code
document.addEventListener("DOMContentLoaded", () => {
    // Sélectionne le corps du tableau où les offres d'emploi seront affichées
    const jobTable = document.querySelector("#job-table tbody");
    // Sélectionne le formulaire pour ajouter ou modifier une offre d'emploi
    const addJobForm = document.querySelector("#add-job-form");
    // Indique si nous sommes en mode d'édition (pour modifier une offre existante)
    let editMode = false;
    // Stocke l'ID de l'offre d'emploi en cours de modification
    let editJobId = null;

    // Fonction asynchrone pour récupérer les offres d'emploi depuis le serveur
    async function fetchJobs() {
        // Envoie une requête GET à admin.php pour lire les offres d'emploi
        const response = await fetch("admin.php?action=read");
        // Convertit la réponse JSON en un tableau d'objets JavaScript
        const jobs = await response.json();

        // Vide le tableau avant de le remplir avec les nouvelles données
        jobTable.innerHTML = "";
        // Parcourt chaque offre d'emploi et crée une ligne dans le tableau avec les détails de l'offre
        jobs.forEach(job => {
            const row = document.createElement("tr");
            // Remplit la ligne avec les détails de l'offre d'emploi
            row.innerHTML = `
                <td>${job.title}</td>
                <td>${job.short_description}</td>
                <td>${job.wage}</td>
                <td>${job.place}</td>
                <td>${job.working_time}</td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn" data-id="${job.id}">Modifier</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${job.id}">Supprimer</button>
                </td>
            `;
            // Ajoute la ligne au tableau
            jobTable.appendChild(row);
        });

        // Attache un écouteur d'événements pour chaque bouton de suppression
        document.querySelectorAll(".delete-btn").forEach(button => button.addEventListener("click", deleteJob));
        // Attache un écouteur d'événements pour chaque bouton de modification
        document.querySelectorAll(".edit-btn").forEach(button => button.addEventListener("click", startEditJob));
    }

    // Fonction asynchrone pour gérer la suppression d'une offre d'emploi
    const deleteJob = async (e) => {
        // Récupère l'ID de l'offre à partir de l'attribut data-id du bouton cliqué
        const id = e.target.dataset.id;
        try {
            // Envoie une requête DELETE à admin.php pour supprimer l'offre
            await fetch(`admin.php?action=delete&id=${id}`, { method: 'DELETE' });
            // Récupère à nouveau les offres d'emploi pour mettre à jour la liste affichée
            fetchJobs();
        } catch (error) {
            // Affiche une erreur dans la console en cas d'échec de la suppression
            console.error("Erreur lors de la suppression de l'offre:", error);
        }
    };

    // Fonction asynchrone pour démarrer le processus de modification d'une offre d'emploi
    const startEditJob = async (e) => {
        // Récupère l'ID de l'offre à partir de l'attribut data-id du bouton cliqué
        const id = e.target.dataset.id;
        // Envoie une requête GET à admin.php pour récupérer toutes les offres d'emploi
        const response = await fetch(`admin.php?action=read`);
        const jobs = await response.json();
        // Trouve l'offre d'emploi à modifier à partir de l'ID
        const job = jobs.find(job => job.id == id);

        // Remplit les champs du formulaire avec les détails de l'offre sélectionnée
        document.querySelector("#title").value = job.title;
        document.querySelector("#short_description").value = job.short_description;
        document.querySelector("#learn_more").value = job.learn_more;
        document.querySelector("#wage").value = job.wage;
        document.querySelector("#place").value = job.place;
        document.querySelector("#working_time").value = job.working_time;

        // Indique que nous sommes en mode édition
        editMode = true;
        // Stocke l'ID de l'offre d'emploi à modifier
        editJobId = id;
    };

    // Écoute l'événement de soumission du formulaire pour ajouter ou mettre à jour une offre d'emploi
    addJobForm.addEventListener("submit", async (e) => {
        // Empêche le comportement par défaut de soumission du formulaire
        e.preventDefault();

        // Crée un objet avec les données du formulaire
        const jobData = {
            title: document.querySelector("#title").value,
            short_description: document.querySelector("#short_description").value,
            learn_more: document.querySelector("#learn_more").value,
            wage: document.querySelector("#wage").value,
            place: document.querySelector("#place").value,
            working_time: document.querySelector("#working_time").value,
        };

        // Vérifie si nous sommes en mode édition
        if (editMode) {
            // Envoie une requête PUT pour mettre à jour l'offre d'emploi
            await fetch(`admin.php?action=update&id=${editJobId}`, {
                method: "PUT",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(jobData)
            });
            // Réinitialise le mode d'édition
            editMode = false;
            // Réinitialise l'ID de l'offre d'emploi à modifier
            editJobId = null;
        } else {
            // Envoie une requête POST pour créer une nouvelle offre d'emploi
            await fetch("admin.php?action=create", {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(jobData)
            });
        }

        // Récupère à nouveau les offres d'emploi pour mettre à jour la liste affichée
        fetchJobs();
        // Réinitialise le formulaire après l'ajout ou la mise à jour
        addJobForm.reset();
    });

    // Appelle la fonction pour récupérer et afficher les offres d'emploi lorsque la page se charge
    fetchJobs();
});