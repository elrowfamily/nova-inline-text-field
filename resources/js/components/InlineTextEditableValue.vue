<template>
  <div
    :class="`nova-inline-text-field-index text-${field.textAlign} w-full`"
  >
      <input
        ref="input"
        v-model="field.value"
        @keyup.enter="updateFieldValue"
        @blur="updateFieldValue"
        type="text"
        :disabled="loading"
        class="form-control form-input form-input-bordered o1-w-full"
        @click.stop.capture="true"
      />
    <!-- thanks to https://github.com/epartment/nova-unique-ajax-field/blob/master/resources/js/components/FormField.vue -->
    <div class="absolute rotating text-80 flex justify-center items-center pin-y pin-r mr-3" v-show="loading">
      <svg class="w-4 h-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M457.373 9.387l-50.095 50.102C365.411 27.211 312.953 8 256 8 123.228 8 14.824 112.338 8.31 243.493 7.971 250.311 13.475 256 20.301 256h10.015c6.352 0 11.647-4.949 11.977-11.293C48.159 131.913 141.389 42 256 42c47.554 0 91.487 15.512 127.02 41.75l-53.615 53.622c-20.1 20.1-5.855 54.628 22.627 54.628H480c17.673 0 32-14.327 32-32V32.015c0-28.475-34.564-42.691-54.627-22.628zM480 160H352L480 32v128zm11.699 96h-10.014c-6.353 0-11.647 4.949-11.977 11.293C463.84 380.203 370.504 470 256 470c-47.525 0-91.468-15.509-127.016-41.757l53.612-53.616c20.099-20.1 5.855-54.627-22.627-54.627H32c-17.673 0-32 14.327-32 32v127.978c0 28.614 34.615 42.641 54.627 22.627l50.092-50.096C146.587 484.788 199.046 504 256 504c132.773 0 241.176-104.338 247.69-235.493.339-6.818-5.165-12.507-11.991-12.507zM32 480V352h128L32 480z" class=""></path></svg>
    </div>
  </div>
</template>

<script>
import EditIcon from '../icons/EditIcon';
import CancelIcon from '../icons/CancelIcon';
import ConfirmIcon from '../icons/ConfirmIcon';
import InteractsWithResourceInformation from 'nova/mixins/InteractsWithResourceInformation';

export default {
  props: ['resourceName', 'field'],
  mixins: [InteractsWithResourceInformation],
  components: { EditIcon, CancelIcon, ConfirmIcon },

  data: () => ({
    editing: false,
    loading: false,
    fieldValue: '',
  }),

  mounted() {
    this.fieldValue = this.field.value;
  },

  methods: {
    onInputKeyPress(e) {
      if (e.which === 13) this.updateFieldValue();
    },

    startEditing() {
      if (this.editing) return;
      this.fieldValue = typeof this.field.value === 'number' ? this.field.value || '' : (this.field.value || '').trim();
      this.editing = true;

      this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
    },

    cancelEditing() {
      if (this.loading) return;
      this.editing = false;
    },

    async updateFieldValue() {
      this.loading = true;
      try {
        await Nova.request().post(`/nova-vendor/nova-inline-text-field/update/${this.resourceName}`, {
          _inlineResourceId: this.field.resourceId,
          _inlineAttribute: this.field.attribute,
          [this.field.attribute]: this.fieldValue,
        });
        this.editing = false;
        this.field.value = this.fieldValue;

        Nova.success(
          this.__('The :resource was updated!', {
            resource: this.resourceInformation.singularLabel.toLowerCase(),
          })
        );
      } catch (e) {
        console.error(e);
        Nova.error(this.__('There was a problem submitting the form.'));
      }
      this.loading = false;
    },
  },

  computed: {
    hasValue() {
      return this.field.value !== null;
    },
  },
};
</script>

<style lang="scss">
.nova-inline-text-field-index {
  position: relative;
  display: flex;
  align-items: center;

  > .edit-icon {
    height: 24px;
    width: 24px;
    margin-right: 2px;
    margin-bottom: 1px;
    flex-shrink: 0;
    min-width: 24px;
    cursor: pointer;
    padding: 4px;

    &:hover {
      fill: rgba(var(--colors-primary-500));
    }
  }

  > .cancel-icon,
  > .confirm-icon {
    height: 24px;
    width: 24px;
    cursor: pointer;
    margin-left: 6px;
    opacity: 0.6;

    &:hover {
      opacity: 1;
    }
  }

  > .cancel-icon {
    fill: #f87171;
  }

  > .confirm-icon {
    fill: #4ade80;
  }

  > .form-input {
    margin-right: 8px;
    max-width: 50vw;

    height: 1.75rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    font-size: 14px;
    max-height: calc(100% - 2px);
  }
}
</style>
