document.addEventListener("DOMContentLoaded", () => {
    fetch('admin.php?action=read') 
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur de chargement des données");
            }
            return response.json();
        })
        .then(jobs => {
            const jobList = document.getElementById("job-list");
            jobList.innerHTML = ""; 

            if (jobs.length === 0) {
                jobList.innerHTML = "<p class='text-center'>Aucune offre d'emploi disponible.</p>";
                return;
            }

            jobs.forEach(job => {
                const jobCard = `
                    <div class="col-12 job-column mb-4">
                        <div class="card job-card">
                            <div class="card-body">
                                <h5 class="card-title job-title">${job.title}</h5>
                                <p class="card-text job-description">${job.short_description}</p>
                                <p class="card-text"><strong>Salaire :</strong> ${job.wage} | <strong>Lieu :</strong> ${job.place}</p>
                                <p class="card-text"><strong>Temps de travail :</strong> ${job.working_time}</p>
                                <button class="btn btn-primary learn-more-btn" data-id="${job.id}">En savoir plus</button>
                            </div>
                        </div>
                    </div>
                `;
                jobList.innerHTML += jobCard;
            });

            // Ajouter un event listener à tous les boutons "En savoir plus"
            document.querySelectorAll('.learn-more-btn').forEach(button => {
                button.addEventListener('click', event => {
                    const jobId = event.target.getAttribute('data-id');
                    loadJobDetails(jobId); // Charger les détails de l'annonce correspondante
                });
            });
        })
        .catch(error => {
            const jobList = document.getElementById("job-list");
            jobList.innerHTML = `<p class='text-danger text-center'>Erreur : ${error.message}</p>`;
        });
});

// Fonction pour charger les détails d'une annonce
function loadJobDetails(id) {
    fetch(`admin.php?action=read&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur de chargement des détails de l'annonce");
            }
            return response.json();
        })
        .then(job => {
            // Créer ou modifier la div à droite pour afficher les détails de l'annonce
            const jobDetailsDiv = document.getElementById('job-details');
            jobDetailsDiv.innerHTML = `
                <h3>Détails de l'annonce</h3>
                <h4>${job.title}</h4>
                <p><strong>Description complète :</strong> ${job.learn_more}</p>
                <p><strong>Salaire :</strong> ${job.wage}</p>
                <p><strong>Lieu :</strong> ${job.place}</p>
                <p><strong>Temps de travail :</strong> ${job.working_time}</p>
                <button class="btn btn-success mt-3" id="apply-button">Postuler</button>
            `;
            // Ajouter un event listener au bouton "Postuler"
            document.getElementById("apply-button").addEventListener('click', () => {
                // Redirection vers la page de postulation avec l'ID de l'annonce
                window.location.href = `postuler.php?job_id=${job.id}`;
            });
        })
        .catch(error => {
            alert(`Erreur : ${error.message}`);
        });
}