Vue.component('cards-component', {
    data: function () {
        return {
            count: 0
        }
    },
    template: `
        <div class="cards-display">
            <h3>{{ name }}</h3>
            <ul class="single-line-list">
              <card-display
                  v-bind:style="{width:card_width,maxWidth:card_width,minWidth:card_width}"
                  v-for="card in cards"
                  v-bind:show_reserve_button="show_reserve_button"
                  v-bind:show_card_buttons="show_card_buttons"
                  v-bind:player="player"
                  v-bind:key="card.id"
                  v-bind:card="card" 
                  v-on:buy="buy($event)"
                  v-on:reserve="reserve($event)">
              </card-display>
            </ul>
        </div>
    `
});

new Vue({
    el: '#cards'

});
