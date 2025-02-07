{!! view_render_event('admin.leads.create.contact_person.form_controls.before') !!}

<v-contact-persons-component></v-contact-persons-component>

{!! view_render_event('admin.leads.create.contact_person.form_controls.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-contact-persons-component-template">
        <div class="flex flex-col gap-2">
            <!-- Lista de personas de contacto -->
            <div v-for="(contact, index) in contacts" :key="index" class="flex gap-4 items-center">
                <!-- Búsqueda de persona -->
                <div class="flex-1">
                    <x-admin::form.control-group>
                        <x-admin::lookup
                            src="{{ route('admin.contacts.persons.search') }}"
                            v-bind:name="`persons[${index}]`"
                            v-bind:params="{query: contact.name}"
                            @selected="personSelected($event, index)"
                            placeholder="@lang('admin::app.leads.common.contact.name')"
                            v-bind:value="{id: contact.id, name: contact.name}"
                        />
                    </x-admin::form.control-group>
                </div>

                <!-- Email de la persona -->
                <div class="flex-1" v-if="contact.email">
                    <span class="text-gray-600" v-text="contact.email"></span>
                </div>

                <!-- Botón eliminar -->
                <div class="flex items-center" v-if="contacts.length > 1">
                    <button 
                        type="button"
                        class="text-red-600 hover:text-red-800"
                        @click="removeContact(index)"
                    >
                        <span class="icon-delete text-2xl"></span>
                    </button>
                </div>
            </div>

            <!-- Botón agregar persona -->
            <div class="flex justify-start">
                <button 
                    type="button"
                    class="secondary-button"
                    @click="addContact"
                >
                    @lang('admin::app.leads.common.contact.add-person')
                </button>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-contact-persons-component', {
            template: '#v-contact-persons-component-template',

            data() {
                return {
                    contacts: [{
                        id: null,
                        name: '',
                        email: ''
                    }]
                };
            },

            methods: {
                personSelected(person, index) {
                    if (person && person.id) {
                        this.contacts[index] = {
                            id: person.id,
                            name: person.name,
                            email: person.emails && person.emails.length > 0 ? person.emails[0].email : ''
                        };
                    }
                },

                addContact() {
                    this.contacts.push({
                        id: null,
                        name: '',
                        email: ''
                    });
                },

                removeContact(index) {
                    if (this.contacts.length > 1) {
                        this.contacts.splice(index, 1);
                    }
                }
            }
        });
    </script>
@endPushOnce