# cluster-ha-nxlog — Rocky Linux 9.8

Pacote inicial de HA ativo/passivo para dois nós NXLog, usando Keepalived/VRRP para mover o VIP e Ansible para instalação/configuração.

## Topologia

- `nxlog-node-01`: `192.168.15.106` (preferencial, prioridade 150)
- `nxlog-node-02`: `192.168.15.107` (reserva, prioridade 100)
- VIP: `192.168.15.108/24` (NÃO configure este IP estaticamente nas interfaces)
- NXLog: serviço systemd `nxlog`, listener esperado em TCP/UDP 514
- Dashboard PHP: `/var/www/html/nxlog/`

O nó que possui o VIP é o ativo. O Keepalived monitora o serviço NXLog e a porta 514; se a checagem falhar, o nó perde prioridade e o VIP migra. O modo padrão é failback/preempção: quando o node-01 voltar saudável, ele pode reassumir o VIP. Ajuste `nopreempt` se preferir evitar failback automático.

> IMPORTANTE: VRRP exige conectividade L2/multicast ou unicast VRRP permitido entre os nós. Libere protocolo IP 112 entre `192.168.15.106` e `192.168.15.107`. Confirme também que a rede permite o VIP e gratuitous ARP. O VIP `.108` deve estar reservado para este cluster e não pode estar em uso por outro equipamento.

## O que vem no ZIP

- Playbook e inventário Ansible
- Templates Keepalived e script de health-check
- CLI `cluster-ha-nxlog` com status/start/stop/migrate/set-primary
- Serviço systemd para monitor local
- Dashboard PHP simples com endpoint JSON
- Variáveis centralizadas em `group_vars/all.yml`

## 1. Ajuste obrigatório antes de instalar

Edite `inventory/hosts.ini` e `group_vars/all.yml`:
- `ansible_user`
- `cluster_interface` (descubra com `ip -br addr`)
- `cluster_vip_cidr` se a máscara não for `/24`
- `cluster_vrrp_password` (máximo 8 caracteres no Keepalived)
- `nxlog_service_name` se o serviço não for `nxlog`
- `nxlog_check_port` e `nxlog_check_protocol` conforme o listener real

Confirme se `192.168.15.108` NÃO está configurado estaticamente nos dois hosts.

## 2. Validar NXLog antes

Em cada nó:
```bash
sudo systemctl status nxlog
sudo ss -lntup | grep ':514'
sudo firewall-cmd --list-all
```
O health-check verifica serviço ativo e listener TCP por padrão. Se o NXLog escutar somente UDP, altere `nxlog_check_protocol: udp`.

## 3. Instalar via Ansible

Na máquina de controle Ansible:
```bash
cd cluster-ha-nxlog
ansible all -i inventory/hosts.ini -m ping
ansible-playbook -i inventory/hosts.ini playbooks/site.yml --ask-become-pass
```
O playbook instala Keepalived, CLI, checker e serviço de monitor. Não altera a configuração interna do NXLog.

## 4. Comandos

```bash
sudo cluster-ha-nxlog status
sudo cluster-ha-nxlog start
sudo cluster-ha-nxlog stop
sudo cluster-ha-nxlog migrate
sudo cluster-ha-nxlog set-primary nxlog-node-02
sudo cluster-ha-nxlog set-primary nxlog-node-01
```

`migrate` transfere o VIP para o outro nó de forma controlada. `stop` para o HA nos dois nós (via SSH) e remove o VIP; `start` inicia NXLog e Keepalived nos dois nós. `set-primary` define a preferência alterando a prioridade do Keepalived nos dois nós e reiniciando o serviço. Use apenas um operador por vez.

## 5. Dashboard PHP

Copie/instale pelo playbook em `/var/www/html/nxlog`. Abra:
`http://<IP-do-servidor>/nxlog/`

O dashboard lê o estado local em `/var/lib/cluster-ha-nxlog/status.json`. O timer systemd atualiza o arquivo a cada 5 segundos. O usuário do PHP precisa apenas de leitura nesse arquivo. Restrinja o acesso HTTP conforme a política da sua rede.

## 6. Testes recomendados

1. Verifique `cluster-ha-nxlog status` e confirme VIP em apenas um nó.
2. Execute `migrate` e confirme que o VIP mudou.
3. Pare NXLog no ativo (`systemctl stop nxlog`) e confirme a migração automática.
4. Desligue o ativo e confirme failover.
5. Restaure o nó e valide o comportamento de failback.

## Segurança e limitações

- O pacote é uma base operacional, não substitui teste em homologação.
- SSH sem senha deve funcionar do nó onde o CLI é executado para o peer, com `sudo` sem senha para os comandos necessários. O playbook instala regra sudoers restrita para o usuário Ansible; revise-a.
- O Keepalived `auth_pass` tem limite prático de 8 caracteres e não deve ser tratado como segurança forte.
- O script `stop`/`start` usa SSH para coordenar os dois nós. Execute como root.
- Para evitar split-brain, não opere manualmente Keepalived nos dois nós ao mesmo tempo; garanta VRRP funcional e monitore a rede.
- Se o listener for UDP, teste envio real de syslog além do teste de socket UDP (UDP não confirma serviço remoto como TCP).
=======
# cluster-nxlog
cluster-nxlog
>>>>>>> ecf356cb3dd8817bd3fb2924a8c77629ac5ae510
