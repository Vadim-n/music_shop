<template>
    <div class="form-group">
        <select :multiple="multiple" class="form-control" :class="{'form-control-sm': isSm}"
                :size="size"
                id="exampleFormControlSelect1" v-model="result"
                @change="returnInput($event)">
            <option v-if="!multiple && clearChoice" value="">Очистить выбор</option>
            <option v-for="option in options" :value="option.value" :data-name="option.label">
                {{'-'.repeat(option.level)}}{{option.label}}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "customSelect",
        model: {
            prop: 'selected',
            event: 'change',
        },
        props: {
            options: {
                type: Array,
                default: []
            },
            multiple: {
                type: Boolean,
                default: false
            },
            selected: {
                default: []
            },
            isSm: {
                type: Boolean
            },
            size: {
                default: null
            },
            clearChoice: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                result: null
            };
        },
        created() {
            this.updateResult();
        },
        watch: {
            selected(newVal, oldVal) {
                if (newVal != oldVal) {
                    this.updateResult();
                }
            }
        },
        methods: {
            returnInput($event) {
                if (!this.multiple) {
                    let result = _.find(this.options, option => {
                        return option.value == this.result;
                    });
                    result = result ? result : null;
                    this.$emit('change', result);
                    return;
                } else {
                    let result = _.filter(this.options, option => {
                        return this.result.indexOf(option.value) > -1;
                    });
                    this.$emit('change', result);
                    return;
                }
            },
            updateResult() {
                if (!this.multiple) {
                    this.result = this.selected ? this.selected.value : null;
                } else {
                    this.result = _.map(this.selected, option => {
                        return option.value;
                    });
                    if (!this.result) {
                        this.result = [];
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>
