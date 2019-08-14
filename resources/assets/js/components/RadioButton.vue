<script>
    export default {
        name: "radio-button",
        props: ['urlUpdate', 'value'],
        data() {
            return {
                'notification_status': false,
            }
        },
        mounted() {
            if (this.value == 1) {
               this.notification_status = true;
            }
        },
        methods: {
            toggleEnableDisable()
            {
                this.notification_status = !this.notification_status;
                axios.put(this.urlUpdate, {
                    notification_status: this.notification_status
                })
                .then((response) => {
                    console.log(response);
                })
                .catch(function (error) {
                    alert(error);
                });
            }
        }
    }
</script>

<template>
    <div>
        <button type="button"
        id="allow_email_notification"
        class="btn btn-sm btn-toggle"
        data-toggle="button"
        aria-pressed="true"
        autocomplete="on"
        v-bind:class="{active: notification_status }"
        @click="toggleEnableDisable"
        >
            <div class="handle"></div>
        </button>
    </div>
</template>