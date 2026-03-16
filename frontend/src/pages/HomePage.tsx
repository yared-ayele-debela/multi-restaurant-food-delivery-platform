import { Link } from 'react-router-dom'
import { useAuth } from '../context/useAuth'

export function HomePage() {
  const { user, ready } = useAuth()

  return (
    <div className="fd-page">
      <h1>Food delivery</h1>
      <p className="fd-lead">Browse restaurants, build a cart, and check out.</p>
      <p>
        <Link to="/restaurants">Find restaurants</Link>
        {' · '}
        <Link to="/cart">Cart</Link>
        {ready && user ? (
          <>
            {' · '}
            <Link to="/orders">Your orders</Link>
          </>
        ) : null}
      </p>
      {!ready ? (
        <p className="fd-muted">Loading session…</p>
      ) : user ? (
        <p>
          Signed in as <strong>{user.name}</strong>.{' '}
          <Link to="/account">Account</Link>.
        </p>
      ) : (
        <p>
          <Link to="/login">Log in</Link> or <Link to="/register">register</Link> to place orders.
        </p>
      )}
    </div>
  )
}
