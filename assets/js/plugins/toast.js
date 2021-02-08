class Toast {
  constructor(position = "top right", timeout = 5000) {
    this.position = position.split(" ");
    this.timeout = timeout;
    this.container = document.createElement("div");
    this.container.classList.add("toast-container");
    this.container.classList.add(this.position[0]);
    this.container.classList.add(this.position[1]);
    document.body.prepend(this.container);
  }
  success(msg) {
    const toast = document.createElement("div");
    toast.classList.add("toast");
    toast.classList.add("opaque");
    toast.classList.add("success");
    toast.innerHTML = `&#10003&nbsp;${msg}`;
    this.container.appendChild(toast);

    setTimeout(() => {
      this.container.removeChild(toast);
    }, this.timeout);
  }

  error(msg) {
    const toast = document.createElement("div");
    toast.classList.add("toast");
    toast.classList.add("opaque");
    toast.classList.add("error");
    toast.innerHTML = `&#x1F5D9;&nbsp;${msg}`;
    this.container.appendChild(toast);

    setTimeout(() => {
      this.container.removeChild(toast);
    }, this.timeout);
  }

  warning(msg) {
    const toast = document.createElement("div");
    toast.classList.add("toast");
    toast.classList.add("opaque");
    toast.classList.add("warning");
    toast.innerHTML = `&#9888;&nbsp;${msg}`;
    this.container.appendChild(toast);

    setTimeout(() => {
      this.container.removeChild(toast);
    }, this.timeout);
  }

  info(msg) {
    const toast = document.createElement("div");
    toast.classList.add("toast");
    toast.classList.add("opaque");
    toast.classList.add("info");
    toast.innerHTML = `&#8505;&nbsp;${msg}`;
    this.container.appendChild(toast);

    setTimeout(() => {
      this.container.removeChild(toast);
    }, this.timeout);
  }

  confirm(message, options = false) {
    const confirm = document.createElement("div");
    confirm.classList.add("toastr-confirm");
    confirm.innerHTML = `<div class='toastr-confirm-body'>
    <h3 class='toastr-title text-red t-2'>&#9888;</h3>
    <div class='toastr-title'>${message}</div>
    <div class='toastr-actions'>
      <button id='toastr-btn-no' class='btn btn-danger'>&#x1F5D9;&nbsp;Cancel</button>
      <button id='toastr-btn-yes' class='btn btn-primary'>Confirm&nbsp;&#10003</button>
    </div></div>`;

    const check = this.container.childNodes.forEach(node => node.isEqualNode(confirm));
    if (!check) {
      this.container.appendChild(confirm);
      document.querySelector("#toastr-btn-no").addEventListener(
        "click",
        () => {
          this.container.removeChild(confirm);
        },
        true
      );
      document.querySelector("#toastr-btn-yes").addEventListener(
        "click",
        () => {
          this.container.removeChild(confirm);
          if (options !== false) options.yes();
        },
        true
      );
    }
  }
}

const toastr = new Toast();
