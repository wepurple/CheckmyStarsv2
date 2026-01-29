document.addEventListener("DOMContentLoaded", function() {
    const carouselContainer = document.getElementById('photoCarousel');
    const content = document.getElementById('carouselContent');

    if (carouselContainer && content) {
        // Récupération sécurisée des données PHP
        const imagesRaw = carouselContainer.getAttribute('data-images');
        const imagesList = imagesRaw ? JSON.parse(imagesRaw) : [];
        
        let html = '<div class="carousel-item active"><div class="row g-3 justify-content-center">';
        
        // 1. Affichage des photos de l'hôtel
        imagesList.forEach((imgLien) => {
            html += `
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 bg-dark border-secondary shadow-sm">
                        <img src="${imgLien}" class="card-img-top" 
                             style="height: 200px; object-fit: cover; border-radius: 10px;" 
                             alt="Photo">
                    </div>
                </div>`;
        });

        // 2. Bouton "Ajouter" pointant vers img/ajout-image.png
        html += `
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card h-100 bg-dark border-secondary shadow-sm" 
                     style="cursor:pointer; min-height: 200px;" 
                     data-bs-toggle="modal" 
                     data-bs-target="#uploadModal">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="text-center">
                        <i class="fa-solid fa-plus"></i>
                            <p class="mt-2 text-secondary small">Ajouter une photo</p>
                        </div>
                    </div>
                </div>
            </div>`;

        html += '</div></div>';
        content.innerHTML = html;
    }
});