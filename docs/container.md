# Slimer Container

Slimer use [Pimple](http://pimple.sensiolabs.org/) as its DI container.

## Service Provider

Pimple has service provider mechanish to extend the container. Slimer implements many useful service Provider in the Slimer-core and the Slimer root project path App/Provider

```PHP
use Pimple\Container;

class FooProvider implements Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        // register some services and parameters
        // on $pimple
    }
}
```

> Providers config 

Please refer [Provider config](suit-config?id=providers)

## Builtin service in Container

<table>
<th><td>Name</td><td>Desc</td><td>Sample</td></th>
<tr><td>1</td><td>request</td><td>The Slim request object</td><td>$this->request->getParams()['newCoreid'];</td></tr>
<tr><td>2</td><td>response</td><td>The Slim response object</td><td>$this->response->withRedirect('/group/supervisor_deligate');</td></tr>
<tr><td>3</td><td>flash</td><td>The flash component object. Like Flask flash to show the message within session.
</td><td>$this->flash->addMessage('success','successfuly added a new deligater ' . $newCoreId);</td></tr>
<tr><td>4</td><td>dbDefault</td><td>The Slimer DB engine object</td><td> $this->dbGam->insert($deligate->getTable(), $data);</td></tr>
<tr><td>5</td><td>router</td><td>The Slim router object</td><td> $this->router->pathFor($name, $data) === $this->uri->getBasePath() . '/' . ltrim($this->uri->getPath(), '/');</td></tr>
<tr><td>6</td><td>session</td><td>The Session helper object</td><td>$this->session->get('user')['loginName'];</td></tr>
<tr><td>7</td><td>logger</td><td>The monolog component which have been registed in DIC when at the beginning.</td><td>Use it directly in the Controller function  
$this->logger->debug('----start delete');
</td></tr>
<tr><td>8</td><td>entity</td><td>The entity factory method.</td><td>$deligate = $this->entity('SuperDeligate')</td></tr>
<tr><td>9</td><td>util</td><td>The Utility helper object</td><td>$this->util->ldapCheck($newCoreId)->toArray();</td></tr>
<tr><td>10</td><td>config</td><td>The Slimer config object</td><td>$this->config('auth.ldap.fields.login', ['uid', 'mail']);</td></tr>
<tr><td>11</td><td>container</td><td>The Slimer original container object</td><td>$this->container->has('user') && $this->container->get('user')</td></tr>
<tr><td>12</td><td>httpClient</td><td>The http request client service.</td><td>$response = $this->httpClient()->request('GET',"{$artConf['host']}/artifactory/api/security/users/{$IDs}",['auth' => [$artConf['username'], $artConf['password']]]);</td></tr>
<tr><td>13</td><td>shellCommand</td><td>The php shell command helpler.</td><td>$command = $this->container['shellCommand'](['command'=>$cmd,'useExec'=>false]); $command->execute();</td></tr>
<tr><td>14</td><td>user</td><td>The current user in the session.</td><td>$this->user->get('type') != 'ldap'</td></tr>
<tr><td>15</td><td>ldap_client</td><td>The ldap client class.</td><td>$ldap = $this->container['ldap_client']('auth.ldap.server');
        $ldap->bind($this->container['config']('auth.ldap.admin.dn'), $this->container['config']('auth.ldap.admin.password'));
        $collection = $ldap->query($this->container['config']('auth.ldap.baseDN'), '(' . 'motguid=' . $coreid .')')->execute();</td></tr>
<tr><td>16</td><td>ldap_entry</td><td>The ldap entry class.</td><td>$entry = $this->ldap_entry("cn={$group[1]},ou=groups,ou=jenkins,ou=Applications,ou=Intranet,dc=motorola,dc=com",['uniqueMember'=>$members]); $ldap->getEntryManager()->update($entry);</td></tr>
</table>



