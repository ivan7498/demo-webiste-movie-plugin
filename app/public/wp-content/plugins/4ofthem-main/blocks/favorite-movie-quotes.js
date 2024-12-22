const { registerBlockType } = wp.blocks;
const { TextControl, SelectControl, PanelBody } = wp.components;
const { InspectorControls } = wp.blockEditor;
const { useState, useEffect } = wp.element;

registerBlockType('movie-plugin/favorite-movie-quotes', {
    title: 'Favorite Movie Quote',
    icon: 'format-quote',
    category: 'common',
    attributes: {
        quote: {
            type: 'string',
            default: '',
        },
        movie: {
            type: 'string',
            default: '',
        },
        author: {
            type: 'string',
            default: '',
        },
    },
    supports: {
        postTypes: ['movie'],
    },
    edit: ({ attributes, setAttributes }) => {
        const { quote, movie, author } = attributes;
        const [movies, setMovies] = useState([]);

        useEffect(() => {
            wp.apiFetch({ path: '/wp/v2/movie?per_page=100' }).then((movies) => {
                setMovies(movies);
            });
        }, []);

        return (
            <div className="favorite-movie-quote-block">
                <InspectorControls>
                    <PanelBody title="Select Movie">
                        <SelectControl
                            label="Movie"
                            value={movie}
                            options={[
                                { label: 'Select a Movie', value: '' },
                                ...movies.map((movie) => ({ label: movie.title.rendered, value: movie.id })),
                            ]}
                            onChange={(value) => setAttributes({ movie: value })}
                        />
                    </PanelBody>
                </InspectorControls>
                <TextControl
                    label="Quote"
                    value={quote}
                    onChange={(value) => setAttributes({ quote: value })}
                />
                <TextControl
                    label="Author"
                    value={author}
                    onChange={(value) => setAttributes({ author: value })}
                />
            </div>
        );
    },
    save: ({ attributes }) => {
        const { quote, movie, author } = attributes;
        return (
            <blockquote className="favorite-movie-quote">
                <p>{quote}</p>
                <cite>{author}</cite>
            </blockquote>
        );
    },
});
