
// Dodavanje slušatelja događaja za odabir žanra
document.addEventListener('DOMContentLoaded', function () {
    const genreSelect = document.getElementById('genre-filter');
    const movieList = document.getElementById('movie-list');

    if (genreSelect) {
        genreSelect.addEventListener('change', function () {
            const selectedGenre = genreSelect.value;
            console.log('Odabrani žanr:', selectedGenre);

            // Fetch zahtjev za filtriranje filmova
            fetch(`/wp-json/movie-plugin/v1/movies?genre=${selectedGenre}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Primljeni podaci:', data);
                    movieList.innerHTML = ''; // Brisanje postojećeg sadržaja

                    if (data.length) {
                        data.forEach(movie => {
                            const movieCard = document.createElement('div');
                            movieCard.classList.add('movie-card');
                            movieCard.innerHTML = `
                                <h3>${movie.title}</h3>
                                <div class="movie-thumbnail">
                                    <img src="${movie.thumbnail}" alt="${movie.title}">
                                </div>
                                <p>${movie.excerpt}</p>
                            `;
                            movieList.appendChild(movieCard);
                        });
                    } else {
                        movieList.innerHTML = '<p>Nema dostupnih filmova za ovaj žanr.</p>';
                    }
                })
                .catch(error => console.error('Greška prilikom dohvaćanja filmova:', error));
        });
    }
});
