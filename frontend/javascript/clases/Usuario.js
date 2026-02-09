export class Usuario {
  constructor({
    id_usuario = null,
    nombre = '',
    correo = '',
    contrasena = null,
    usuario_activo = true,
    rol = 10
  } = {}) {

    rol = rol ?? {};

    this._id_usuario = id_usuario;
    this._nombre = nombre;
    this._correo = correo;
    this._contrasena = contrasena;
    this._usuario_activo = Boolean(usuario_activo);
    this._rol = {
      id_rol: rol.id_rol ?? null,
      rol: rol.rol ?? ''
    };
  }

// ====PASAR A JSON====
  toJSON() {
    const data = {
      id_usuario: this._id_usuario,
      nombre: this._nombre,
      correo: this._correo,
      usuario_activo: this._usuario_activo ? 1 : 0,
      id_rol: this._rol.id_rol
    };

    if (this._contrasena) data.contrasena = this._contrasena;

    return data;
  }

  // ======================
  // GETTERS Y SETTERS
  // ======================

  get id_usuario() {
    return this._id_usuario;
  }

  set id_usuario(valor) {
    this._id_usuario = valor;
  }

  get nombre() {
    return this._nombre;
  }

  set nombre(valor) {
    this._nombre = valor;
  }

  get correo() {
    return this._correo;
  }

  set correo(valor) {
    this._correo = valor;
  }

  get usuario_activo() {
    return this._usuario_activo;
  }

  set usuario_activo(valor) {
    this._usuario_activo = Boolean(valor);
  }

  get rol() {
    return this._rol;
  }

  set rol(valor) {
    this._rol = {
      id_rol: valor.id_rol || null,
      rol: valor.rol || ''
    };
  }
}
