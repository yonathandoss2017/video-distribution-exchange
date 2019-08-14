<script>
    export default {
        name: "distribution-playlist",
        props: ['property', 'tod', 'selectedPlaylists'],
        data(){
            return {
                search: '',
                playlists: [],
                selectedPlaylistData: [],
                data					: {
                    data				: [],
                    links				: {},
                    meta				: {},
                },
                offset					: 4,
                page                    : 1,
                total                   : 0,
                from                    : 0,
                to                      : 0,
            }
        },
        methods: {
            searchPlaylist() {
                this.page = 1;
                this.fetchPlaylists();
            },
            fetchPlaylists(){
                let url = '/manage/'+ this.property.id +'/cp/distributions/' + this.tod.id +'/playlists/ajax/query';
                axios.get(url, {
                    params: {
                        'search': this.search,
                        'page': this.page
                    }
                })
                    .then((response) => {
                        this.playlists = response.data.data;
                        this.data = response.data;
                        this.total = response.data.meta.total;
                        this.from = response.data.meta.from;
                        this.to = response.data.meta.to;

                    }).catch((error) => {
                    alert(error);
                });
            },
            confirm(){
                let playlists = [];
                _.each(this.selectedPlaylistData, function (value) {
                   playlists.push(value.id);
                });

                if (playlists.length > 0) {
                    axios.put('/manage/'+ this.property.id +'/cp/distributions/' + this.tod.id +'/playlists', {
                        'playlists': playlists
                    })
                        .then((response) => {
                            window.location = '/manage/'+ this.property.id +'/cp/exchange/distribution/' + this.tod.id +'/edit';
                        }).catch((error) => {
                        alert(error);
                    });
                } else {
                    alert('Opps! No playlists selected.');
                }
            },
            goToPage: function (page){
                this.isLoading = true;
                let url = this.data.links.first;
                console.log(this.data.links);
                console.log(url);
                this.page = page;
                this.fetchPlaylists();
            },
            select(id){
                let playlist = _.find(this.playlists, function (p) {
                    return p.id == id;
                });
                let findSelectedPlaylist = _.find(this.selectedPlaylistData, function (p) {
                    return p.id == id;
                });
                if (!findSelectedPlaylist) {
                    this.selectedPlaylistData.push(playlist);
                }
            },
            removeSelected(id){
                let filtered = _.filter(this.selectedPlaylistData, function (p) {
                    return p.id != id;
                });
                this.selectedPlaylistData = filtered;
            }
        },
        computed: {
            pagesNumber: function () {
                if (!this.data.meta.to) {
                    return [];
                }
                let from = this.data.meta.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.data.meta.last_page) {
                    to = this.data.meta.last_page;
                }
                let pagesArray = [];
                for (let page = from; page <= to; page++) {
                    pagesArray.push(page);
                }
                return pagesArray;
            }
        },
        mounted()
        {
            this.fetchPlaylists();
            this.selectedPlaylistData = this.selectedPlaylists;
        },
        updated() {
            var box = document.getElementsByClassName('table');

            if ( box[0].offsetHeight > box[1].offsetHeight) {
                box[1].setAttribute( 'style', 'height: ' + box[0].offsetHeight + 'px; margin-bottom: 0;');
                if (box[0].offsetHeight === 910)
                    box[1].parentNode.setAttribute('style', 'max-height: 910px; margin-bottom: 30px;');
            } else {
                if (box[1].offsetHeight < 911)
                    box[0].style.height = box[1].offsetHeight + 'px';
            }
        }
    }
</script>

<style scoped>
    .playlist-title { width: 334px;}
    .title-button-header { float: none;}
    .scrolled-v { overflow-y: auto;}
</style>