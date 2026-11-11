# Checklist pré-implantação

- [ ] Confirmar máscara/rede do VIP (exemplo atual usa /24).
- [ ] Confirmar interface com `ip -br addr`.
- [ ] Confirmar `.108` livre e reservado exclusivamente ao cluster.
- [ ] Confirmar NXLog ativo e escutando em TCP ou UDP 514 nos dois nós.
- [ ] Confirmar mesmo `virtual_router_id` e senha nos dois nós.
- [ ] Liberar IP protocol 112 (VRRP) entre os dois nós; validar firewall/rede.
- [ ] Confirmar SSH sem senha entre os nós e sudo apropriado.
- [ ] Fazer backup de `/etc/keepalived/` e das configurações existentes.
- [ ] Aplicar primeiro em janela de manutenção e validar failover/failback.
